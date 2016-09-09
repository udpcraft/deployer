(function ($) {
    $.fn.extend({
        timerStart:function(options){
            var timer;
            var $this=$(this);
            var $leftTime=$this.find('#leftTime');
            clearInterval(timer);
            var defaults={leftTime:50,closeCallBack:function(){}};
            var settings = jQuery.extend({}, defaults, options);
            var fnClose=function(){
                clearInterval(timer);
                $this.slideUp();
                settings.closeCallBack();
            }
            $this.find('.close').on('click',function(){
                fnClose();
            })
            var leftTime=settings.leftTime;
            $this.slideDown();
            var fnStart=function(){
                leftTime--;
                $leftTime.html(leftTime);
                if(leftTime<=0){
                    fnClose();
                }
            }
            fnStart();
            timer=setInterval(fnStart,1000);

        }
    });
    $.fn.extend({
        ajaxPostForm:function(options){
            var $thisForm=$(this);
            options.formAction=options.formAction||$thisForm.attr('action');
            options.processApi=options.processApi||'';//获取进度条的api
            options.closeProcessApi=options.closeProcessApi||'';//关闭进度条的api
            if(!options.processApi){
                return;
            }
            var proceeDom='<div class="modal fade" id="modalProcessBar" tabindex="-1" role="dialog"'
                +'ria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">'
                +'<div class="modal-dialog" style="margin:300px auto">'
                +'<div class="modal-content">'
                +'<div class="modal-header">'
                +'<button type="button" class="close" data-dismiss="modal"'
                +'ria-hidden="true">×'
                +'</button>'
                +'<h4 class="modal-title" id="myModalLabel">wait...'
                +'</h4>'
                +'</div>'
                +'<div class="modal-body">'
                +'<div class="progress active">'
                +'<div id="prcWidth" class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width: 5%">'
                +'<span id="prcText">5%</span>'
                +'</div>'
                +'</div>'
                +'</div>'
                +'</div>'
                +'</div>'
                +'</div>';

            $thisForm.off('submit').on('submit',function(){
                if($('#modalProcessBar').length==0){
                    $('.content').append(proceeDom);
                }else{
                    $('#modalProcessBar').remove();
                    $('.content').append(proceeDom);
                }
                var $progressBar=$('#modalProcessBar').find('.progress-bar');
                var $progressBar_span=$progressBar.find('span');
                var $title=$('#modalProcessBar').find('#myModalLabel');

                //如果form表单通过表单验证
                var boolIsValid=$thisForm.data('bootstrapValidator').isValid();
                if(boolIsValid) {

                    var timer;
                    var getProcessData=function(){
                        $.get(options.processApi).then(function(data){
                            //根据data的值修改 progress-bar
                            var status=data.data.status;
                            var msg=data.data.msg;
                            var total_step=data.data.total_steps;//总步骤
                            var current_step=data.data.current_step;//当前第几步
                            var pers=(current_step*100/total_step).toFixed('0')+'%';
                            if(status==0){
                                $title.text(' Error（'+ msg +'）');
                                clearInterval(timer);
                                return;
                            }
                            $progressBar.css('width',pers);
                            $progressBar_span.text(pers+' Complete ('+ msg +')');
                            $title.text(pers+' Complete ('+ msg +')');
                            if(status==2){
                                setTimeout(function(){
                                    clearInterval(timer);
                                    $('#modalProcessBar').modal('hide');
                                    window.location.href=window.location.href.replace('project','diff');
                                },2000)
                            }

                        },function(){
                            $title.text(' Error（系统错误，请联系管理员！！！）');
                        });
                    }
                    //ajax form表单提交
                    $.post(options.formAction,$thisForm.serialize()).then(function(data){
                    });
                    clearInterval(timer);
                    timer=setInterval(getProcessData,2000);//2s种请求一次

                    //模态窗方法和事件
                    $('#modalProcessBar').modal({backdrop: 'static',keyboard: false}).on('hide.bs.modal', function () {
                        //关闭进度条接口
                        $.get(options.closeProcessApi).then(function(){
                            clearInterval(timer);
                        });
                        $('#modalProcessBar').remove();
                        $thisForm.find('button[type=submit]').attr('disabled',false);
                    });
                }
                else{
                    $thisForm.data('bootstrapValidator').validate();
                }
                return false;
            });
        }
    });
    $.extend({
        processBar:function(options){
            options.postApi=options.postApi||'';
            options.processApi=options.processApi||'';//获取进度条的api
            options.closeProcessApi=options.closeProcessApi||'';//关闭进度条的api
            /*if(!options.processApi){
             return;
             }*/
            var proceeDom='<div class="modal fade" id="modalProcessBar" tabindex="-1" role="dialog"'
                +'ria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="true">'
                +'<div class="modal-dialog" style="margin:300px auto">'
                +'<div class="modal-content">'
                +'<div class="modal-header">'
                +'<button type="button" class="close" data-dismiss="modal"'
                +'ria-hidden="true">×'
                +'</button>'
                +'<h4 class="modal-title" id="myModalLabel">release..'
                +'</h4>'
                +'</div>'
                +'<div class="modal-body">'

                +'<div class="overlay"> <i class="fa fa-refresh fa-spin"></i> </div>'
                +'</div>'

                +'</div>'
                +'</div>'
                +'</div>';

            if($('#modalProcessBar').length==0){
                $('.content').append(proceeDom);
            }else{
                $('#modalProcessBar').remove();
                $('.content').append(proceeDom);
            }
            var timer;
            var arrMsg=[];
            var getProcessData=function(){
                $.get(options.processApi).then(function(data){
                    //根据data的值修改 progress-bar
                    if(data.data) {
                        var status = data.data.status;
                        var msg = data.data.msg;
                        var isExit = function () {
                            var flag = false;
                            for (var i = 0; i < arrMsg.length; i++) {
                                if (arrMsg[i] == msg) {
                                    flag = true;
                                }
                            }
                            return flag;
                        }
                        var flag = isExit();
                        if (!flag) {
                            arrMsg.push(msg);
                        }
                        if (status == 0) {
                            if (!flag) {
                                var p = '<p><span class="fa fa-remove"  style="color:red"></span> ' + msg + ' </p>';
                                $('.overlay').before(p).hide();
                            }
                            clearInterval(timer);
                            return;
                        }
                        else if (status == 1 || status == 2) {
                            if (!flag) {
                                var p = '<p><span class="glyphicon glyphicon-ok" style="color:#00c0ef"></span> ' + msg + ' </p>';
                                $('.overlay').before(p);
                            }
                            if (status == 2) {
                                setTimeout(function () {
                                    clearInterval(timer);
                                    $('#modalProcessBar').modal('hide');
                                    window.location.reload();
                                }, 2000)

                            }
                        }
                    }else{
                        var p='<p><span class="fa fa-remove"  style="color:red"></span> 数据返回错误！</p>';
                        $('.overlay').before(p).hide();
                        clearInterval(timer);
                    }


                },function(){
                    var p='<p><span class="fa fa-remove"  style="color:red"></span> 系统错误请联系管理员！</p>';
                    $('.overlay').before(p).hide();
                    clearInterval(timer);

                });
            }
            $.post(options.postApi).then(function(data){
            });
            clearInterval(timer);
            timer=setInterval(getProcessData,2000);//2s种请求一次

            //模态窗方法和事件
            $('#modalProcessBar').modal({backdrop: 'static',keyboard: false}).on('hide.bs.modal', function () {
                //关闭进度条接口
                $.get(options.closeProcessApi).then(function(){
                    clearInterval(timer);
                });
                $('#modalProcessBar').remove();
            });

            return false;
        }
    });
    $(function(){
        var $del= $('.row-remove');
        var $modal_del=$('#modal-delete');
        if($del.length){
            $del.on('click',function(){
                $modal_del.modal('show');
                $('#modal-id').val($(this).attr('href-id'));

            });
            window.delData=function(url){
                $("#modal-delete .btn-ok").on('click', function(e){
                    var id=$('#modal-id').val();
                    url=url+'?id='+id;
                    $.get(url).then(function(data){
                        if(data.data.status){
                            $modal_del.modal('hide');
                            window.location.reload();
                        }else{
                            alert('删除失败！');
                            $modal_del.modal('hide');
                        }
                    });
                });
            }

        }
    })
})(window.jQuery);