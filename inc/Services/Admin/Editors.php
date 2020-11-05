<?php


namespace MAM\Plugin\Services\Admin;


use MAM\Plugin\Services\ServiceInterface;

class Editors implements ServiceInterface
{

    /**
     * @inheritDoc
     */
    public function register()
    {
        add_action( 'admin_menu', array($this, 'remove_menus' ));
    }

    public function remove_menus(){
        if ( current_user_can( 'editor' ) ){
            remove_menu_page( 'edit.php?post_type=page' );
            remove_menu_page( 'edit.php' );
            remove_menu_page( 'edit-comments.php' );
            remove_menu_page( 'tools.php' );
        }
    }
}