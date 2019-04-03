<?php

namespace App\Admin\Extensions\Actions;

use App\Models\RealNameAuth;
use Encore\Admin\Admin;

class RealNameAuthCheck
{
    protected $realNameAuth;
    protected $newStatus;
    protected $fontawesome;
    protected $confirmText;

    /**
     * RealNameAuthCheck constructor.
     * @param RealNameAuth $realNameAuth
     * @param $newStatus
     * @param $fontawesome
     * @param $confirmText
     */
    public function __construct(RealNameAuth $realNameAuth, $newStatus, $fontawesome, $confirmText)
    {
        $this->realNameAuth = $realNameAuth;
        $this->newStatus = $newStatus;
        $this->fontawesome = $fontawesome;
        $this->confirmText = $confirmText;
    }

    protected function script()
    {
        return <<<SCRIPT

$('.grid-check-{$this->fontawesome}').unbind('click').click(function() {

    swal({
        title: '{$this->confirmText}',
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "确认",
        showLoaderOnConfirm: true,
        cancelButtonText: "取消",
        preConfirm: function() {
            return new Promise(function(resolve) {
                $.ajax({
                    method: 'post',
                    url: '/admin/real_name_auths/{$this->realNameAuth->id}',
                    data: {
                        _method:'PUT',
                        _token:LA.token,
                        status:'{$this->newStatus}',
                    },
                    success: function (data) {
                        $.pjax.reload('#pjax-container');
                        resolve(data);
                    }
                });
            });
        }
    }).then(function(result) {
        var data = result.value;
        if (typeof data === 'object') {
            if (data.status) {
                swal(data.message, '', 'success');
            } else {
                swal(data.message, '', 'error');
            }
        }
    });
});

SCRIPT;

    }

    protected function render()
    {
        Admin::script($this->script());
        return "<a href='javascript:void(0);' class='grid-check-{$this->fontawesome}'><i class='fa {$this->fontawesome}'></i></a>";
    }

    public function __toString()
    {
        // TODO: Implement __toString() method.
        return $this->render();
    }
}