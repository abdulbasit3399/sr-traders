<?php echo e(Form::model($vender,array('route' => array('vender.update', $vender->id), 'method' => 'PUT'))); ?>

<div class="modal-body">

    <h5 class="sub-title"><?php echo e(__('Basic Info')); ?></h5>
    <div class="row">
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                <?php echo e(Form::label('name',__('Name'),array('class'=>'form-label'))); ?>

                <div class="form-icon-user">
                    <span><i class="ti ti-address-card"></i></span>
                    <?php echo e(Form::text('name',null,array('class'=>'form-control','required'=>'required'))); ?>

                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-6">
            <div class="form-group">
                <?php echo e(Form::label('contact',__('Contact'),['class'=>'form-label'])); ?>

                <div class="form-icon-user">
                    <span><i class="ti ti-mobile-alt"></i></span>
                    <?php echo e(Form::text('contact',null,array('class'=>'form-control','required'=>'required'))); ?>

                </div>
            </div>
        </div>
        <div class="col-lg-12 col-md-12 col-sm-6">
            <div class="form-group">
                <?php echo e(Form::label('billing_address',__('Address'),array('class'=>'form-label'))); ?>

                <div class="input-group">
                    <?php echo e(Form::textarea('billing_address',null,array('class'=>'form-control','rows'=>3))); ?>

                </div>
            </div>
        </div>
        
        <?php if(!$customFields->isEmpty()): ?>
            <div class="col-lg-4 col-md-4 col-sm-6">
                <div class="tab-pane fade show" id="tab-2" role="tabpanel">
                    <?php echo $__env->make('customFields.formBuilder', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="row">
        
    </div>

    <?php if(App\Models\Utility::getValByName('shipping_display')=='on'): ?>
        <div class="col-md-12 text-end">
            
        </div>
        
        <div class="row">
            
        </div>
    <?php endif; ?>

</div>

<div class="modal-footer">
    <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn btn-primary">
</div>

<?php echo e(Form::close()); ?>

<?php /**PATH C:\laragon\www\accountgo\resources\views/vender/edit.blade.php ENDPATH**/ ?>