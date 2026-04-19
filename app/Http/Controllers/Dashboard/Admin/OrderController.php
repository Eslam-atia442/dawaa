<?php
namespace App\Http\Controllers\Dashboard\Admin;

use App\Enums\OrderStatusFlagEnum;
use App\Http\Controllers\BaseWebController;
use App\Http\Requests\Admin\Order\CreateRequest;
use App\Http\Requests\Admin\Order\UpdateRequest;
use App\Models\Order;
use App\Exports\OrderExport;
use App\Jobs\ExportJob;
use App\Models\User;
use App\Services\ExportService;
use Exception;
use App\Services\OrderService;
use App\Services\RefundService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use App\Repositories\SQL\StoreRepository;


class OrderController extends BaseWebController
{
    public object $service;
    public object $userService;
    public string $table;
    public string $guard;
    public array $relations;
    protected ExportService $exportService;
    protected RefundService $refundService;

    public function __construct(
                        UserService $userService,
                        OrderService $service,
                        RefundService $refundService,
                        ExportService $exportService,
                        $table = 'orders',
                        $guard = 'admin'
    )
    {

        $this->service = $service;
        $this->userService = $userService;
        $this->refundService = $refundService;
        $this->exportService = $exportService;
        $this->table = $table;
        $this->guard = $guard;
        $this->relations = [];
        parent::__construct($this->service, $this->table, $this->guard, $this->relations ,'order');
    }
   
    
   
    public function index(): View|JsonResponse
    {


        if (request()->ajax()) {
            $rows = $this->service->search(request()->all(), $this->relations);
            $html = view('dashboard.' . $this->guard . '.' . $this->table . '.table', compact('rows'))->render();
            return response()->json(['html' => $html  ]);
        }
        $users = $this->userService->search(['limit' => false, 'page' => false], [], );
        $stores = app(StoreRepository::class)->search(['limit' => false, 'page' => false, 'active' => true], [], []);
        return view('dashboard.' . $this->guard . '.' . $this->table . '.index' , compact('users', 'stores'));
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
    public function update(UpdateRequest $request, Order $order): JsonResponse
    {
        try {
            $this->service->update($order, $request->validated());
            return response()->json(['url' => route($this->guard . '.' . $this->table . '.index')]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()] , 400);
        }
    }

    public function toggleField(Request $request, $order, $key)
    {
        return $this->service->toggleField($order, $key);
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
                ->filter(function ($value) {
                    if (is_array($value)) {
                        return !empty(array_filter($value, fn($v) => $v !== '' && $v !== null));
                    }
                    return $value !== '' && $value !== null;
                })
                ->map(function ($value) {
                    if (is_array($value)) {
                        return array_filter($value, fn($v) => $v !== '' && $v !== null);
                    }
                    return $value;
                })
                ->toArray();

            $export = $this->exportService->createExport(
                name: __('trans.order.index') . ' ' . __('trans.export_excel') . ' - ' . now()->format('Y-m-d H:i:s'),
                model: 'Order',
                parameters: $filters
            );

            ExportJob::dispatch(
                export: $export,
                exportClass: OrderExport::class,
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

    public function approveRefund(Order $order): JsonResponse
    {
        try {
            $order->load(['parentOrder.items.product', 'parentOrder.items.childProduct', 'items']);

            $approvedRefund = $this->refundService->approveRefund($order);

            return response()->json([
                'success' => true,
                'message' => __('trans.refund_approved_successfully'),
                'refund' => $approvedRefund
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function rejectRefund(Order $order): JsonResponse
    {
        try {
            $order->load(['parentOrder.items.product', 'parentOrder.items.childProduct', 'items']);

            $rejectedRefund = $this->refundService->rejectRefund($order);

            return response()->json([
                'success' => true,
                'message' => __('trans.refund_rejected_successfully'),
                'refund' => $rejectedRefund
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateOrderStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'order_status' => ['required', 'integer', 'in:1,2,3,4'],
        ]);

        try {
            $order->update(['order_status' => (int) $request->order_status]);
            $order->refresh();
            $statusValue = $order->order_status?->value ?? OrderStatusFlagEnum::IN_PROGRESS->value;
            return response()->json([
                'success' => true,
                'message' => __('trans.order_status_updated_successfully'),
                'order_status' => $statusValue,
                'order_status_label' => OrderStatusFlagEnum::getTranslation($statusValue),
                'order_status_color' => OrderStatusFlagEnum::getColor($statusValue),
            ]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
