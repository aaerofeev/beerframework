<?php if (count($errors) > 0): ?>
    <div class="b-errors">
    <?php foreach ($errors as $key => $error): ?>
        <span><?php echo $error ?></span>
    <?php endforeach ?>
    </div>
<?php endif ?>