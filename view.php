<?php
function MC_plugin_settings_init()
{
    add_settings_section(
        'MC-plugin-media-cloud-section',
        'Media Cloud Configuration',
        'MC_plugin_media_cloud_section_callback',
        'MC-plugin-settings'
    );
    $api_key = get_option('md_edu_key');
	if (!$api_key) {
			add_settings_field(
				'media-cloud-api-key',
				'Media Cloud API Key',
				'MC_plugin_media_cloud_api_key_callback',
				'MC-plugin-settings',
				'MC-plugin-media-cloud-section'
			);

			add_settings_field(
				'media-cloud-new-account-button',
				'',
				'MC_plugin_media_cloud_new_account_button',
				'MC-plugin-settings',
				'MC-plugin-media-cloud-section'
			);
 	}
    
	
	add_settings_field(
        'media-cloud-check-box',
        '',
        'MC_plugin_media_cloud_checkbox',
        'MC-plugin-settings',
        'MC-plugin-media-cloud-section'
    );
	
	
}
add_action('admin_init', 'MC_plugin_settings_init');

function MC_plugin_media_cloud_section_callback()
{
    echo 'Enter your Media Cloud configuration details:';
}

function MC_plugin_media_cloud_api_key_callback()
{
		     echo '<div class="form-group keep key-cloud"><input type="password" name="md_edu_key" required value="" /><span class="show-btn eye-key"><i class="fas fa-eye hide-btn"></i></span></div><span class="ms-5 underline-button" id="forgot_button" data-bs-toggle="modal" data-bs-target="#forgot_Modal">Forgot your key?</span>';

}



function MC_plugin_media_cloud_new_account_button()
{
 
    echo '<span class="underline-button" id="new_account_button" data-bs-toggle="modal" data-bs-target="#Cr_Acc_Modal">You don\'t have key yet? Let\'s create it quickly!</span>';

}

function MC_plugin_media_cloud_checkbox()
{
	$check_del = 1 == get_option('md-check-del') ? 'checked' : '';

	echo '<div class="form-check">
					  <label class="form-check-label" for="flexCheckChecked">
						Delete files from the web drive immediately after uploading.
						
					  </label>
					  <input class="form-check-input" type="checkbox" value="" id="flexCheckChecked" '. $check_del .' name="md-check-del">
					</div>';
}

function MC_plugin_register_settings()
{
    register_setting('MC-plugin-settings', 'media-cloud-api-key');
}
add_action('admin_init', 'MC_plugin_register_settings');



class MCView {
    private $options;
    function __construct(){
        if (is_admin()) {
        add_action('admin_menu', array($this, 'add_page'));
        add_action('admin_init', array($this, 'init_page'));
        } else{
            // add_action('init', array($this, 'create_page'));
            // add_action('init', array($this, 'init_page'));
        }
    }
    public function create_page(){
        $this->options = get_option('MC_option');
        //dd($this->options);
        require_once MC_PATH . 'settings/index.php';
    }
    
    public function add_page(){
        add_options_page(
            'Settings Admin',
            'Media Cloud',
            'manage_options',
            'MediaCloud-admin',
            array($this, 'create_page')
        );
    }
    public function sanitize($input){
        $new_input = array();
        
        if(isset($input['user_key'])){
            $new_input['user_key'] = sanitize_text_field($input['user_key']);
        }
        if(isset($input['active'])){
            $new_input['active'] = sanitize_text_field($input['active']);
        }
        if(isset($input['draft'])){
            $new_input['draft'] = sanitize_text_field($input['draft']);
        }
        if(isset($input['yt'])){
            $new_input['yt'] = sanitize_text_field($input['yt']);
        }
        if(isset($input['show_yt'])){
            $new_input['show_yt'] = sanitize_text_field($input['show_yt']);
        }
        if(isset($input['time'])){
            $time = sanitize_text_field($input['time']);
            if ($time == '' || $time < 30) {
                $time = 30;
            }
            $new_input['time'] = $time;
        }
        if(isset($input['crawl_type'])){
            $crawl_type = sanitize_text_field($input['crawl_type']);
            if ($crawl_type == '') {
                $crawl_type = 'keyword';
            }
            $new_input['crawl_type'] = $crawl_type;
        }
        if(isset($input['local'])){
            $new_input['local'] = sanitize_text_field($input['local']);
        }
        if(isset($input['user'])){
            $user = sanitize_text_field($input['user']);
            if ($user == '') {
                $user = 1;
            }
            $new_input['user'] = $user;
        }
        if(isset($input['title_type'])){
            $title_type = sanitize_text_field($input['title_type']);
            if ($title_type == '') {
                $title_type = 'source';
            }
            $new_input['title_type'] = $title_type;
        }
        if(isset($input['proxy'])){
            $new_input['proxy'] = sanitize_text_field($input['proxy']);
        }
        if(isset($input['remove_domain'])){
            $new_input['remove_domain'] = sanitize_text_field($input['remove_domain']);
        }
        if(isset($input['time_publish'])){
            $time_publish = sanitize_text_field($input['time_publish']);
            if ($time_publish == '' || $time_publish < 0) {
                $time_publish = 0;
            }
            $new_input['time_publish'] = $time_publish;
        }
        if(isset($input['ping_time'])){
            $ping_time = sanitize_text_field($input['ping_time']);
            if ($ping_time == '' || $ping_time < 300) {
                $ping_time = 900;
            }
            $new_input['ping_time'] = $ping_time;
        }
        if(isset($input['log'])){
            $new_input['log'] = sanitize_text_field($input['log']);
        }

        return $new_input;
    }
    public function init_page(){
        register_setting(
            'MC_option_group',
            'MC_option',
            array($this, 'sanitize')
        );
        add_settings_section(
            'section_id',
            '',
            array($this, 'section_info'),
            'MediaCloud'
        );
    }
    public function create_callback($args){
        
    }
    public function section_info(){
    }

    
}
if(is_admin()){
    new MCView();
}
// $currentPath = $_SERVER['REQUEST_URI'];
// if (substr($currentPath, -2) === 'MC') {
//     MC_script();
//     add_action('wp_enqueue_scripts', 'MC_script');
//     new MCView();

// }
?>