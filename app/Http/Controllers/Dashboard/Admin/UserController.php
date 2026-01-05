<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\User\CreateRequest;
use App\Http\Requests\Admin\User\UpdateRequest;
use App\Models\User;
use App\Services\ExportService;
use App\Jobs\ExportJob;
use App\Exports\UserExport;
use Exception;
use App\Enums\UserTypeEnum;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Services\CountryService;
use App\Mail\User\AccountAcceptedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends BaseWebController
{
    public object $service;
    public string $table;
    public string $guard;
    public array $relations;
    public object $countryService;
    protected ExportService $exportService;
    
    public function __construct(
        UserService $service,
        CountryService $countryService,
        ExportService $exportService,
        $table = 'users',
        $guard = 'admin'
    )
    {
        $this->service = $service;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = ['country'];  
        $this->countryService = $countryService;
        $this->exportService = $exportService;
        parent::__construct($this->service, $this->table, $this->guard, $this->relations ,'user');
    }

    
    public function create(): View
    {
        $countries = $this->countryService->search(['limit' => false, 'page' => false , 'active' => true ], [], []);
        $userTypes = collect(UserTypeEnum::cases())->map(function ($type) {
            return ['id' => $type->value, 'name' => $type->label()];
        })->toArray();
        return view('dashboard.' . $this->guard . '.' . $this->table . '.create', compact('countries', 'userTypes'));
    }

    public function edit($id): View
    {
        $row = $this->service->find($id, $this->relations);
        $countries = $this->countryService->search(['limit' => false, 'page' => false , 'active' => true ], [], []);
        $userTypes = collect(UserTypeEnum::cases())->map(function ($type) {
            return ['id' => $type->value, 'name' => $type->label()];
        })->toArray();
        return view('dashboard.' . $this->guard . '.' . $this->table . '.edit', compact('row', 'countries', 'userTypes'));
    }

    public function store(CreateRequest $request): JsonResponse
    {
        try {
            $this->service->create($request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }
    public function update(UpdateRequest $request, User $user): JsonResponse
    {
        try {
            $this->service->update($user, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }
    public function toggleField(Request $request, $user, $key)
    {
        return $this->service->toggleField($user, $key);
    }

    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,block,unblock',
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:users,id'
        ]);

        try {
            $result = $this->service->bulkAction($request->input('action'), $request->input('ids'));

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'updated_count' => $result['count']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'data' => 'required|json'
        ]);

        return $this->destroy($request->input('data'));
    }

    public function export(Request $request): JsonResponse
    {
        try {
            $filters = collect($request->except(['_token']))
                ->filter(fn($value) => $value !== '' && $value !== null)
                ->toArray();

            $export = $this->exportService->createExport(
                name: __('trans.user.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'User',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: UserExport::class,
                filters: $filters
            );

            return response()->json([
                'success' => true,
                'message' => __('trans.export_queued'),
                'export_id' => $export->id
            ]);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function acceptAccount(User $user): JsonResponse
    {
        try {
            if ($user->is_accepted) {
                return response()->json([
                    'success' => false,
                    'message' => __('trans.account_already_accepted')
                ], 400);
            }

            if (!$user->email) {
                return response()->json([
                    'success' => false,
                    'message' => __('trans.user_email_required')
                ], 400);
            }

            $password = Str::random(12);
            
            $user->update([
                'is_accepted' => true,
                'password' => $password,
            ]);

            Mail::to($user->email)->send(new AccountAcceptedMail($user, $password));

            return response()->json([
                'success' => true,
                'message' => __('trans.account_accepted_successfully')
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
