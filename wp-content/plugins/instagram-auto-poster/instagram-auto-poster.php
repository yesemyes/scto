<?php
/*
Plugin Name: Instagram Auto Poster | Shared by Themes24x7.com
Plugin URI: http://sominus.net/instagram-auto-poster/
Description: The plugin allows to publish in your account instagram your posts.
Version: 1.0.1
Author: Vladimir Yakovlev
Author URI: http://sominus.net
*/

require_once 'instagram-api/Instagram.php';

$instagramAutoPoster = new InstagramAutoPoster();
$instagramAutoPoster->init();

class InstagramAutoPoster {
    protected $pageName = 'instagram-auto-poster.php';
    protected $settingsGroup = 'instagram_auto_poster_settings';
    protected $domain = 'instagram-auto-poster';
    
    public function init() {
        load_plugin_textdomain($this->domain, false, dirname(plugin_basename(__FILE__)) . '/languages');
           
        add_action('admin_head', array($this, 'admincss'));
        add_action('admin_head', array($this, 'adminjs'));
        add_action('admin_menu', array($this, 'plugin_options'));
        add_action('admin_init', array($this, 'register_settings'));
        
        add_filter('publish_post', array($this, 'onPostSave'));
        
        add_action('wp_ajax_instagram_send', array($this, 'ajaxSend'));
        
        add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_action('instagram_scheduled', array($this, 'publishToInstagram'), 10, 3);
        
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'settings_link'));

        if ($this->getOption('columns') == "on") {
            add_filter('manage_edit-post_columns', array($this, 'add_column'), 4);
            add_filter('manage_edit-page_columns', array($this, 'add_column'), 4);
        
            add_filter('manage_posts_custom_column', array($this, 'fill_column'), 5, 2);
            add_filter('manage_pages_custom_column', array($this, 'fill_column'), 5, 2);
        }
    }
    
    public function settings_link($links) {
        $links[] = '<a href="'. esc_url(get_admin_url(null, 'options-general.php?page=instagram-auto-poster.php')) .'">Settings</a>';
        return $links;
    }
    
    protected function getOption($name) {
        $options = get_option($this->settingsGroup);
        if (empty($options) || !is_array($options)) {
            return null;
        }

        return $options[$name];
    }
    
    public function plugin_options() {
        add_options_page(
            __('Settings - Instagram Auto Poster', $this->domain),
            __('Instagram Auto Poster', $this->domain),
            'manage_options',
            $this->pageName,
            array($this, 'plugin_options_page')
        );
    }
    
    public function plugin_options_page() {
        echo '
        <div class="wrap">
            <h2>' . __('Instagram Auto Poster', $this->domain) . '</h2>
            <p>' . __('The plugin allows to publish in your account instagram your posts.', $this->domain) . '</p>
            
            <form method="post" enctype="multipart/form-data" action="options.php">
        ';
                
        echo settings_fields($this->settingsGroup);
        echo do_settings_sections($this->pageName);
        
        echo '  <p class="submit">
                    <input type="submit" class="button-primary" value="' . __('Save Changes', $this->domain) . '" />
                </p>
            </form>
        </div>
        ';
    }
    
    public function register_settings() {
        register_setting($this->settingsGroup, $this->settingsGroup, array($this, 'true_validate_settings'));

        add_settings_section('instagram_section', __('Settings', $this->domain), '', $this->pageName);
        
        //login
        add_settings_field(
            'login',
            __('Instagram login', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type'      => 'text',
                'id'        => 'login',
                'required'  => 'required="required"',
                'desc'      => '',
                'label_for' => 'login'
            )
        );
        
        //passw
        add_settings_field(
            'passw',
            __('Instagram password', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type'      => 'password',
                'id'        => 'passw',
                'required'  => 'required="required"',
                'desc'      => '',
                'label_for' => 'passw'
            )
        );
        
        //status
        add_settings_field(
            'status',
            __('Plugin status', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type' => 'radio',
                'id'   => 'status',
                'vals' => array(
                    'on' => __('Send new posts in instagram when publishing', $this->domain),
                    'off' => __('Do not send automatically', $this->domain),
                )
            )
        );
        
        //make
        add_settings_field(
            'make',
            __('How to make auto-posts?', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type' => 'radio',
                'id'   => 'make',
                'vals' => array(
                    'off' => __('Use WP Cron to Schedule autoposts', $this->domain),
                    'on' => __('Publish Immediately', $this->domain),
                )
            )
        );
        
        //columns
        add_settings_field(
            'columns',
            __('Display a column in the list of posts?', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type' => 'radio',
                'id'   => 'columns',
                'vals' => array(
                    'on' => __('Enable', $this->domain),
                    'off' => __('Disable', $this->domain),
                )
            )
        );
        
        //htags
        add_settings_field(
            'format',
            __('Message text format', $this->domain),
            array($this, 'display_input_field'),
            $this->pageName,
            'instagram_section',
            array(
                'type' => 'textarea',
                'id'   => 'format',
                'desc'      => implode("<br>", array(
                    "<b>{TITLE}</b> - Inserts the Title of the post",
                    "<b>{URL}</b> - Inserts the URL of the post",
                    "<b>{EXCERPT}</b> - Inserts the excerpt of the post",
                    "<b>{TAGS}</b> - Inserts post tags",
                    "<b>{CATS}</b> - Inserts post categories",
                    "<b>{HCATS}</b> - Inserts post categories as hashtags",
                    "<b>{HTAGS}</b> - Inserts post tags as hashtags",
                    "<b>{AUTHORNAME}</b> - Inserts the author's name",
                    "<b>{SITENAME}</b> - nserts the the Blog/Site name"
                )),
                'label_for' => 'format'
            )
        );
    }
        
    public function true_validate_settings($input) {
        foreach ($input as $k => $v) {
            $valid_input[$k] = trim($v);
        }
        return $valid_input;
    }
    
    public function addMetaBox() {
        $screens = array('post');
        foreach ($screens as $screen) {
            add_meta_box('instagram_box', __('Instagram Auto Poster', $this->domain), array($this, 'metaBoxGetPrintCheckResults'), $screen, 'side', 'high');
        }
    }
    
    public function metaBoxGetPrintCheckResults() {
        global $post;
        
        wp_nonce_field(plugin_basename(__FILE__), 'boom_noncename');
        
        $status = get_post_meta($post->ID, "instagram-send", true);
        #var_dump($status);
        if (is_array($status) && isset($status['status']) && $status['status'] == 'fail') {
            echo '<div class="instagram_date"></div>';
            echo '<span class="instagram_send button" data-id="'.$post->ID.'">'.__('Resend to instagram', $this->domain).'</span>';
            echo '<div class="instagram_result">'.$status['message'].'</div>';
        } elseif ((is_array($status) && isset($status['status']) && $status['status'] == 'ok') || $status == "1") {
            echo '<div class="instagram_date">' . $this->getPostedDate($post->ID) . '</div>';
            echo '<span class="instagram_send button" data-id="'.$post->ID.'">'.__('Resend to instagram', $this->domain).'</span>';
            echo '<div class="instagram_result"></div>';
        } elseif ($status == "sending") {
            echo '<div class="instagram_result">'.__('Sending...', $this->domain).'</div>';
        } elseif ($status == "auth-error") {
            echo '<div class="instagram_date"></div>';
            echo '<span class="instagram_send button" data-id="'.$post->ID.'">'.__('Resend to instagram', $this->domain).'</span>';
            echo '<div class="instagram_result">'.__('Authorisation error', $this->domain).'</div>';
        } elseif ($status == "image-error") {
            echo '<div class="instagram_date"></div>';
            echo '<span class="instagram_send button" data-id="'.$post->ID.'">'.__('Resend to instagram', $this->domain).'</span>';
            echo '<div class="instagram_result">'.__('No thumbnail', $this->domain).'</div>';
        } elseif ($status == "not-sent") {
            echo '<div class="instagram_date"></div>';
            echo '<span class="instagram_send button" data-id="'.$post->ID.'">'.__('Resend to instagram', $this->domain).'</span>';
            echo '<div class="instagram_result">'.__('Not sent to instagram', $this->domain).'</div>';
        } else {
            echo __('Not yet sent...', $this->domain);
        }
    }
    
    public function add_column($columns) {
        $arr = array();
        
        foreach ($columns as $key => $title) {
            if ($key == "author") {
                $arr['instagram'] = 'Instagram Auto Poster';
            }
            
            $arr[$key] = $title;
        }

        return $arr;
    }
    
    public function fill_column($column_name, $post_id) {
        if ($column_name != 'instagram') {
            return;
        }
        
        $status = get_post_meta($post_id, "instagram-send", true);
        
        echo '<span class="instagram">';

        if (is_array($status) && isset($status['status']) && $status['status'] == 'fail') {
            echo "Error: <strong>" . $status['message'] . "</strong>";
        } elseif ((is_array($status) && isset($status['status']) && $status['status'] == 'ok') || $status == "1") {
            echo $this->getPostedDate($post_id);
        } elseif ($status == "sending") {
            echo "Sending...";
        } elseif ($status == "auth-error") {
            echo "Error: <strong>Authorisation error</strong>";
        } elseif ($status == "image-error") {
            echo "Error: <strong>No thumbnail</strong>";
        } else {
            echo "Not yet sent...";
        }

        echo '</span>';
    }
    
    public function getPostedDate($post_id) {
        $time = get_post_meta($post_id, 'instagram-time', 1);
        $code = get_post_meta($post_id, 'instagram-code', 1);
                
        return '<p><a href="https://www.instagram.com/p/'.$code.'/" target="_blank">' . __('Posted on', $this->domain) . ' ' . '('.date("d.m.Y H:i", $time).')</a></p>';
    }
    
    public function onPostSave($post_id) {
        if (wp_is_post_revision($post_id) || wp_is_post_autosave($post_id)) {
            return;
        }
        
        if (get_post_type($post_id) != "post") {
            return;
        }
        
        $isSending = $this->isSending($post_id);
        
        if ($this->getOption('make') == "on") {
            if (!$isSending) {
                $this->publishToInstagram($post_id);
                return;
            }
        }
        
        if ($this->getOption('status') == "on") {
            if (!$isSending) {
                update_post_meta($post_id, "instagram-send", "sending");
                wp_schedule_single_event(time() + 1, 'instagram_scheduled', array($post_id));
            }
        } else {
            update_post_meta($post_id, "instagram-send", "not-sent");
        }
    }
    
    public function isSending($post_id) {
        $status = get_post_meta($post_id, "instagram-send", true);
        
        if ((is_array($status) && isset($status['status']) && $status['status'] == 'ok') || $status == "1") {
            return true;
        }
        
        return false;
    }
    
    public function ajaxSend() {
        $post_id = $_POST['post_id'];
        
        $result = $this->publishToInstagram($post_id);
        
        if ($result === true) {
            echo json_encode(array(
                'status' => true,
                'html' => $this->getPostedDate($post_id)
            ));
        } else {
            echo json_encode(array(
                'status' => false,
                'error' => $result
            ));
        }

        wp_die();
    }
    
    public function publishToInstagram($post_id) {
        $login = $this->getOption('login');
        $passw = $this->getOption('passw');
        
        if (empty($login) || empty($passw)) {
            update_post_meta($post_id, "instagram-send", "auth-error");
            return __('Authorisation error', $this->domain);
        }
              
        $Instagram = new Instagram($login, $passw, false);
        
        try {
            $Instagram->login();
        } catch (InstagramException $e) {
            update_post_meta($post_id, "instagram-send", "auth-error");
            return __('Authorisation error', $this->domain);
        }
        
        try {
            $timestamp = time() + get_option('gmt_offset') * 3600;
                                    
            $image = $this->createJPG($post_id); #false or path
            $caption = $this->getCaption($post_id);
            
            if ($image === false) {
                update_post_meta($post_id, "instagram-send", "image-error");
                return __('No thumbnail', $this->domain);
            }
            
            $result = $Instagram->uploadPhoto($image, $caption);
            
            if ($result['status'] == 'ok') {
                update_post_meta($post_id, "instagram-send", $result);
                update_post_meta($post_id, "instagram-time", $timestamp);
                update_post_meta($post_id, "instagram-code", $result['media']['code']);
                return true;
            } else {
                update_post_meta($post_id, "instagram-send", $result);
            }
            
            return false;
        } catch (Exception $e) {
            echo $e->getMessage();
            update_post_meta($post_id, "instagram-exception", $e->getMessage());
        }
    }
    
    public function getImagePath($post_id) {
        return get_attached_file(get_post_thumbnail_id($post_id));
    }
    
    public function createJPG($post_id) {
        $path = $this->getImagePath($post_id);
        $imagedata = getimagesize($path);
        
        $width  = $imagedata[0];
        $height = $imagedata[1];
        $type = $imagedata['mime'];
              
        $tmp_image = imagecreatetruecolor($width, $height);
        
        if ($type == "image/jpeg") {
            $original_image = imagecreatefromjpeg($path);
        } elseif ($type == "image/png") {
            $original_image = imagecreatefrompng($path);
        }
        
        $tmp_dir = get_temp_dir() . basename($path);
        
        imagecopyresampled($tmp_image, $original_image, 0, 0, 0, 0, $width, $height, $width, $height);
        imagejpeg($tmp_image, $tmp_dir, 100);
        
        return $tmp_dir;
    }
    
    public function getCaption($post_id) {
        $format = $this->getOption('format');
        
        $data = array(
            '{TITLE}' => $this->getParam(get_the_title($post_id)),
            '{URL}' => $this->getParam(get_permalink($post_id)),
            '{EXCERPT}' => $this->getParam($this->getExcerpt($post_id)),
            '{TAGS}' => $this->getParam($this->getTags($post_id)),
            '{CATS}' => $this->getParam($this->getCategory($post_id)),
            '{HCATS}' => $this->getParam($this->getHcats($post_id)),
            '{HTAGS}' => $this->getParam($this->getHtags($post_id)),
            '{AUTHORNAME}' => $this->getParam(get_the_author_meta('display_name', get_post_field('post_author', $post_id))),
            '{SITENAME}'  => $this->getParam(get_bloginfo('name'))
        );
        
        $search  = array();
        $replace = array();
        
        foreach ($data as $key => $val) {
            $search[]  = $key;
            $replace[] = $val;
        }
        
        return str_replace($search, $replace, $format);
    }
    
    public function getExcerpt($post_id) {
        global $post;  
        $save_post = $post;
        $post = get_post($post_id);
        setup_postdata($post);
        $output = html_entity_decode(get_the_excerpt(), ENT_QUOTES, 'UTF-8');        
        $post = $save_post;
        return trim($this->strip_all_shortcodes($output));
    }
    
    function strip_all_shortcodes($text){
        $text = preg_replace("/\[[^\]]+\]/", '', $text);  #strip shortcode
        return $text;
    }
    
    public function getParam($value) {
        return $value ? $value : '';
    }
    
    public function getTags($post_id) {
        $arr = array();
        
        foreach (get_the_tags($post_id) as $tag) {
            $arr[] = $tag->name;
        }
        
        return implode(', ', $arr);
    }
    
    public function getCategory($post_id) {
        $arr = array();
        
        foreach (get_the_category($post_id) as $tag) {
            $arr[] = $tag->name;
        }
        
        return implode(', ', $arr);
    }
    
    public function getHcats($post_id) {
        $tags = array();
            
        foreach (get_the_category($post_id) as $cat) {
            $tags[] = mb_strtolower(str_replace(array(' ', '-'), '', $cat->name));
        }
        
        $htags = "";
        foreach ($tags as $tag) {
            $htags .= ", #" . $tag;
        }

        return trim(trim($htags, ','));
    }
    
    public function getHtags($post_id) {
        $tags = array();
                
        foreach (get_the_tags($post_id) as $tag) {
            $tags[] = mb_strtolower(str_replace(array(' ', '-'), '', $tag->name));
        }
        
        $htags = "";
        foreach ($tags as $tag) {
            $htags .= ", #" . $tag;
        }
        
        return trim(trim($htags, ','));
    }
    
    public function admincss() {
        echo <<<HTML
            <style type='text/css'>
                .instagram_result {
                    font-size: 16px;
                    margin: 10px 0 0 0;
                }
            </style>
HTML;
    }

    public function adminjs() {
        echo '
            <script type="text/javascript">
            jQuery(document).ready(function($) {
    
                jQuery(".instagram_send").bind("click", function(){
                    var post_id = jQuery(this).data("id");
                    var data = {
                        action: "instagram_send",
                        post_id: post_id
                    };
    
                    jQuery(".instagram_result").html("'.__('Sending...', $this->domain).'");
                    jQuery.post(ajaxurl, data, function(json) {
                        if (json.status) {
                            jQuery(".instagram_date").html(json.html);
                            jQuery(".instagram_result").html("'.__('Successfully sent', $this->domain).'");
                        } else {
                            jQuery(".instagram_result").html("'.__('Error:', $this->domain).' " + json.error.message);
                        }
                    }, "json");
                });
                
            });
            </script>
        ';
    }
    
    public function display_input_field($args) {
        extract( $args );
        
        $option = $this->getOption($id);
        switch ( $type ) {
            case 'text':
                echo sprintf(
                    "<input class='regular-text' type='text' id='%s' required='required' name='%s[%s]' value='%s' />", $id, $this->settingsGroup, $id, esc_attr(stripslashes($option))
                );
                echo $desc ? "<br /><span class='description'>$desc</span>" : "";
                break;
            case 'password':
                echo sprintf(
                    "<input class='regular-text' type='password' id='%s' required='required' name='%s[%s]' value='%s' />", $id, $this->settingsGroup, $id, esc_attr(stripslashes($option))
                );
                echo $desc ? "<br /><span class='description'>$desc</span>" : "";
                break;
            case 'textarea':
                echo sprintf(
                    "<textarea class='code large-text' cols='50' rows='10' type='text' id='%s' name='%s[%s]'>%s</textarea>", $id, $this->settingsGroup, $id, esc_attr(stripslashes($option))
                );
                echo $desc ? "<br /><span class='description'>$desc</span>" : "";
                break;
            case 'checkbox':
                $checked = ($option == 'on') ? " checked='checked'" :  '';
                echo sprintf(
                    "<label><input type='checkbox' id='%s' name='%s[%s]' %s /> ", $id, $this->settingsGroup, $id, $checked
                );
                echo $desc ? $desc : "";
                echo "</label>";
                break;
            case 'select':
                echo sprintf(
                    "<select id='%s' name='%s[%s]'>", $id, $this->settingsGroup, $id
                );
                foreach($vals as $v => $l){
                    $selected = ($option == $v) ? "selected='selected'" : '';
                    echo "<option value='$v' $selected>$l</option>";
                }
                echo $desc ? $desc : "";
                echo "</select>";
                break;
            case 'radio':
                echo "<fieldset>";
                foreach($vals as $v=>$l){
                    $checked = ($option == $v || !$option) ? "checked='checked'" : '';
                    echo sprintf(
                        "<label><input type='radio' name='%s[%s]' value='%s' %s />%s</label><br />", $this->settingsGroup, $id, $v, $checked, $l
                    );
                }
                echo "</fieldset>";
                break;
        }
    }
}