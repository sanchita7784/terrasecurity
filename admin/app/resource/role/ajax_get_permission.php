<?php

use App\Model\Permission;
use App\Model\Role;

require_once './app/include/PermissionLinks.php';
require_once './app/model/BaseModel.php';
require_once './app/model/Permission.php';

$permission_links = permission_links();

$role_id = $_GET['role_id'];

$permission = new Permission();

$allow_permission_links = $permission->findList("id", "permission_link", ["role_id" => $role_id]);
?>

<div class="row">
    <?php foreach($permission_links as $permission_link => $permission_title): ?>
    <div class="col-sm-6 co-md-3">
        <label>
            <?php $checked = in_array($permission_link, $allow_permission_links) ? "checked='TRUE'" : "" ?>
            <input type="checkbox" name="form_data[permission][<?= $permission_link ?>]" value="1" <?= $checked  ?> />
            <span><?= $permission_title; ?></span>
        </label>
    </div>
    <?php endforeach; ?>
</div>