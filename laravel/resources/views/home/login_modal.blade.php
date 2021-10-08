<div id="loginModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content"><form method="post" action="{{ Route('login') }}" class="form-horizontal">{{ csrf_field() }}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Login</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group clearfix">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-4">Email Address:</label>
                            <div class="col col-xs-8">
                                <input type="text" name="email" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="form-group clearfix">
                        <div class="col-xs-12">
                            <label class="control-label col-xs-4">Password:</label>
                            <div class="col-xs-8">
                                <input type="password" name="password" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <input type="submit" class="btn btn-primary" value="Login">
                </div>
            </form></div>
    </div>
</div>
