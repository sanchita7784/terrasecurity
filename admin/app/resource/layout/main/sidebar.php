<!-- ========== Left Sidebar Start ========== -->
<div class="vertical-menu">

    <div data-simplebar class="h-100">

        <style>
            #sidebar-menu ul li a i {
                font-size: 14px;
            }
        </style>
        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title" data-key="t-menu">Menu</li>
                <?php foreach (App\Menu::get($resource) as $menu): ?>                    
                    <?php if (isset($menu['links'])): ?>
                        <li class="<?= isset($menu['active']) ? "mm-active" : ""   ?>">
                            <a href="javascript: void(0);" class="has-arrow">
                                <span>
                                    <i class="<?= $menu['icon']; ?>"></i>
                                    <?= $menu['title']; ?>
                                </span>
                            </a>
                            <ul class="sub-menu <?= isset($menu['active']) ? "mm-show" : ""   ?>" aria-expanded="false">
                                <?php foreach($menu['links'] as $submenu): ?>                            
                                    <?php if (isset($submenu['links'])): ?>
                                    <li class="<?= isset($submenu['active']) ? "mm-active" : ""   ?>">
                                        <a href="javascript: void(0);" class="has-arrow">
                                            <span>
                                                <i class="<?= $submenu['icon']; ?>"></i>
                                                <?= $submenu['title']; ?>
                                            </span>
                                        </a>
                                        <ul class="sub-menu <?= isset($menu['active']) ? "mm-show" : ""   ?>" aria-expanded="false">
                                            <?php foreach($submenu['links'] as $child_menu): ?>
                                            <li class="<?= isset($child_menu['active']) ? "mm-active" : ""   ?>">
                                                <a href="<?= url_without_query_params($child_menu['r']) ?>" class="<?= isset($child_menu['active']) ? "active" : ""   ?>">
                                                    <span>
                                                        <i class="<?= $child_menu['icon']; ?>"></i>
                                                        <?= $child_menu['title']; ?>
                                                    </span>
                                                </a>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </li>
                                    <?php elseif (isset($submenu['r'])) : ?>
                                    <li class="<?= isset($submenu['active']) ? "mm-active" : ""   ?>">
                                        <a href="<?= url_without_query_params($submenu['r']) ?>" class="<?= isset($submenu['active']) ? "active" : ""   ?>">
                                            <span>
                                                <i class="<?= $submenu['icon']; ?>"></i>
                                                <?= $submenu['title']  ?>
                                            </span>
                                        </a>
                                    </li>
                                    <?php endif; ?>                            
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php elseif (isset($menu['r'])) : ?>
                        <li>
                            <a href="<?= url_without_query_params($menu['r']) ?>" class="<?= isset($menu['active']) ? "active" : ""   ?>">
                                <span>
                                    <i class="<?= $menu['icon']  ?>"></i>                                
                                    <?= $menu['title']  ?>
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>