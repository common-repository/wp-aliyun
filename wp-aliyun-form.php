<?php
/**
 * 创建菜单
 *
 * @since 1.0.0
 */
function wpaliyun_admin_menu() {
	add_menu_page(__('WP阿里云'), __('WP阿里云'), 'manage_options', 'wp_aliyun_setoption', 'wp_aliyun_setoption', '', 122);
	add_action( 'admin_enqueue_scripts', 'wpaliyun_enqueue_my_scripts' );
}

function wpaliyun_enqueue_my_scripts() {
	if (is_my_plugin_screen()) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', WP_ALIYUN_PAGE_URL .'js/jquery.js',false,"1.7.2");
	wp_enqueue_script('jquery');
	wp_enqueue_script('wp-aliyun-working', WP_ALIYUN_PAGE_URL .'js/wp-aliyun-working.js');
	wp_enqueue_style('wp-aliyun-css', WP_ALIYUN_PAGE_URL .'css/wp-aliyun.css');
	}
}

function is_my_plugin_screen() {
    $screen = get_current_screen();
    if (is_object($screen) && $screen->id == 'toplevel_page_wp_aliyun_setoption') {
        return true;
    } else {
        return false;
    }
}

/**
 * 创建一个单元格
 *
 * @since 1.0.0
 */
function wp_aliyun_create_postbox_begin($title,$id='',$style=''){
	echo '<div class="postbox" id="'.$id.'" style="'.$style.'">';
	echo '		<h3 class="hndle"><span>'.$title.'</span></h3>';
	echo '			<div class="inside">';
}

/**
 * 创建一个单元格
 *
 * @since 1.0.0
 */
function wp_aliyun_create_postbox_end(){
	echo '</div></div>';
}

function wp_aliyun_get_select($id,$min,$max,$n)
{ ?>
	<select name="<? echo $id; ?>" id="<? echo $id; ?>">
		<? for($i=$min;$i<=$max;$i++){ ?>
			<option value="<? echo $i; ?>" <? if($i==$n){ echo ' selected="selected" '; }?> ><? echo $i; ?></option>
		<? } ?>
	</select>
<?
}
/**
 * 创建设置页面
 *
 * @since 1.0.0
 */
function wp_aliyun_setoption(){
?>
<div class="wrap" id="stuffbox">
	<div class="metabox-holder">
		<h2>WP阿里云备份</h2>
		<? $logfile = WP_ALIYUN_LOG; ?>
		<input id="logfile" type="hidden" value="<?php echo $logfile; ?>" name="logfile">
		<input id="logpos" type="hidden" value="0" name="logpos">
		<input id="wpaliyunworkingajaxurl" type="hidden" value="<?php echo WP_ALIYUN_PAGE_URL .'wp-aliyun-working.php'; ?>" name="wpaliyunworkingajaxurl">
		<div class="postbox-container metabox-holder">
			<div id="side-sortables" class="meta-box-sortables ui-sortable" style="width:1200px;">
				<div style="width:840px;margin-right:20px;float:left;">
					<? wp_aliyun_create_postbox_begin('备份进度','jindu'); ?>
					<div id="showworking">
					</div>
				  	<div class="clear"></div>
					<? wp_aliyun_create_postbox_end(); ?>
				</div>
					
				<div style="width:260px;float:left;">
					<div class="postbox-container">
						<? wp_aliyun_create_postbox_begin('WP阿里云备份'); ?>
						<form method="post">
						<input type="hidden" value="check_oss" name="action">
						<b>阿里云ID：</b>
						<input class="large-text" type="text" value="<? echo get_option('OSS_ACCESS_ID'); ?>" name="OSS_ACCESS_ID"><br>
						<b>阿里云Key：</b>
						<input class="large-text" type="text" value="<? echo get_option('OSS_ACCESS_KEY'); ?>" name="OSS_ACCESS_KEY"><br>
						<b>备份目录前缀：</b>
						<input class="large-text" type="text" value="<? echo get_option('OSS_PREFIX'); ?>" name="OSS_PREFIX"><br>
						<div id="major-publishing-actions">
							<input type="submit" value="验证" id="check_oss" name="check_oss">
					  	</div>
					  	<div class="clear"></div>
					  	</form>
						<? wp_aliyun_create_postbox_end(); ?>
					</div>
						
					<div class="postbox-container">
						<? wp_aliyun_create_postbox_begin('备份计划'); ?>
						<form method="post">
						<input type="hidden" name="action" value="save_setting">
						<div id="cron-text">
						<b>下次备份时间：</b><? echo wp_aliyun_get_next_cron_time(); ?>
						</div>
						<br>
						<b><input type="checkbox" name="WPALIYUN_BACK_ACT" <? if(get_option('WPALIYUN_BACK_ACT')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 开启计划执行</b>
						<br>
						<b>每天</b><? wp_aliyun_get_select('WPALIYUN_BACK_H',0,23,get_option('WPALIYUN_BACK_H')); ?><b>时</b><? wp_aliyun_get_select('WPALIYUN_BACK_M',0,59,get_option('WPALIYUN_BACK_M')); ?><b>分进行备份。</b>
						<br>
						<b>云端数据保存数量：</b><? wp_aliyun_get_select('WPALIYUN_BACK_SAVE_COUNT',0,60,get_option('WPALIYUN_BACK_SAVE_COUNT')); ?>
						<br><br>
						<b><input type="checkbox" name="WPALIYUN_BAK_DATA" <? if(get_option('WPALIYUN_BAK_DATA')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 是否备份数据库</b>
						<br>
						<b><input type="checkbox" name="WPALIYUN_BAK_THEME" <? if(get_option('WPALIYUN_BAK_THEME')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 是否备份主题</b>
						<br>
						<b><input type="checkbox" name="WPALIYUN_BAK_UPLOADS" <? if(get_option('WPALIYUN_BAK_UPLOADS')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 是否备份上传文件</b>
						<br>
						<b><input type="checkbox" name="WPALIYUN_AUTO_DEL_LOG" <? if(get_option('WPALIYUN_AUTO_DEL_LOG')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 自动删除日志</b>
						<br>
						<b><input type="checkbox" name="WPALIYUN_AUTO_DEL_ZIP" <? if(get_option('WPALIYUN_AUTO_DEL_ZIP')) { echo 'checked="checked"'; } ?> value="1" class="checkbox"> 自动删除备份</b>
						<br>
						<div id="major-publishing-actions">
					  		<input type="submit" value="保存设置" class="button-primary" id="savebackwpup" name="savebackwpup">
					  	</div>
					  	<div class="clear"></div>
						</form>
						<? wp_aliyun_create_postbox_end(); ?>
					</div>
						
					<div class="postbox-container">
						<? wp_aliyun_create_postbox_begin('立刻执行备份'); ?>
						<form method="post">
						<input type="hidden" name="action" value="now">
						
						<div id="major-publishing-actions">
					  		<input type="submit" value="立刻执行备份" class="button-primary" id="savebackwpup" name="savebackwpup">
					  	</div>
					  	<div class="clear"></div>
					  	</div>
						</form>
						<? wp_aliyun_create_postbox_end(); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>
<?
}
?>