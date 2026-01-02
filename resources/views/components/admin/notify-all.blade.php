<div>

    <div class="modal fade text-left" id="notify" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel160">{{__('trans.Send_notification')}}</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{$route}}" method="POST" enctype="multipart/form-data" class="notify-form">
                        @csrf
                        <input type="hidden" name="id" class="notify_id">
                        <input type="hidden" name="notify" class="notify" value="notifications">
                        <div class="row">

                            <div class="col-md-12 col-12" style="margin-bottom: 10px;">
                                <div class="form-group">
                                    <label for="first-name-column"
                                           style="margin-bottom: 10px;">{{__('trans.the_message_in_arabic')}}</label>
                                    <div class="controls">
                                        <textarea name="body_ar" required class="form-control" cols="30"
                                                  rows="10"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-12" style="margin-bottom: 10px;">
                                <div class="form-group">
                                    <label for="first-name-column"
                                           style="margin-bottom: 10px;">{{__('trans.the_message_in_english')}}</label>
                                    <div class="controls">
                                        <textarea name="body_en" required class="form-control" cols="30"
                                                  rows="10"></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer p-0">
                            <button type="submit"
                                    class="btn btn-primary flex-grow-1 send-notify-button">{{__('trans.send')}}</button>
                            <button type="button" class="btn btn-danger flex-grow-1"
                                    data-bs-dismiss="modal">{{__('trans.cancel')}}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="mail" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-enable-otp modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <form action="{{$route}}" method="POST" enctype="multipart/form-data" class="notify-form">
                        @csrf
                        <input type="hidden" name="id" class="notify_id">
                        <input type="hidden" name="notify" class="email" value="email">
                        <div class="col-md-12 col-12">
                            <div class="form-group">
                                <label for="first-name-column">{{__('trans.the_written_text_of_the_email')}}</label>
                                <div class="controls">
                                    <textarea name="message" class="form-control" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="submit"
                                    class="btn btn-primary send-notify-button">{{__('trans.send')}}</button>
                            <button type="button" class="btn btn-primary"
                                    data-dismiss="modal">{{__('trans.cancel')}}</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
