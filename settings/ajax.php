<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once 'function.php';


/**
 *
 *      ___                       ___           ___           ___     
 *     /\  \          ___        /\  \         /\  \         /\__\    
 *     \:\  \        /\  \       \:\  \       /::\  \       /::|  |   
 *      \:\  \       \:\  \       \:\  \     /:/\:\  \     /:|:|  |   
 *      /::\  \      /::\__\      /::\  \   /::\~\:\  \   /:/|:|  |__ 
 *     /:/\:\__\  __/:/\/__/     /:/\:\__\ /:/\:\ \:\__\ /:/ |:| /\__\
 *    /:/  \/__/ /\/:/  /       /:/  \/__/ \/__\:\/:/  / \/__|:|/:/  /
 *   /:/  /      \::/__/       /:/  /           \::/  /      |:/:/  / 
 *   \/__/        \:\__\       \/__/            /:/  /       |::/  /  
 *                 \/__/                       /:/  /        /:/  /   
 *                                             \/__/         \/__/    
 *
 */


add_action('wp_ajax_check_key_ajax_function', 'check_key_ajax_function');
add_action('wp_ajax_nopriv_check_key_ajax_function', 'check_key_ajax_function');
function check_key_ajax_function($filename = null, $url = null, $info = '')
{
	if (isset($_POST)) {
		$key = isset($_POST["md_edu_key"]) ? esc_attr($_POST["md_edu_key"]) : (esc_attr(get_option('md_edu_key')) ?? '');
		$del = isset($_POST["md-check-del"]) ? true : ((get_option('md-check-del') == 1) ? true : false);
		$status = isset($_POST["md-status"]) ? true : false;
		if ($key == '') {
			wp_send_json_error("Naughty boy, you should stop, don't destroy it.");
		}

		$action = isset($_POST["action"]) ? $_POST["action"] : "upload-attachment";
		$domain = $_SERVER['HTTP_HOST'];
		// POST
		$info = json_encode($info);
		$method = 'POST';
		$request_args = array(
			'method' => $method,
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => array(
				'key' => $key,
				'action' => $action,
				'domain' => $domain,
				'filename' => $filename,
				'url' => $url,
				'info' => $info
			),
		);

		$request_args['body'] = ($method === 'POST') ? json_encode($request_args['body']) : $request_args['body'];
		$response = wp_remote_request(API_URL, $request_args);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			check_key_ajax_function();
			// 			wp_send_json_error("Error: " . $error_message);

		} else {
			$response_body = wp_remote_retrieve_body($response);
			$data = json_decode($response_body, true);
			//var_dump($data['notification']);die;
			if (isset($data['notification']) && $data['notification'] == 'Successful authentication') {
				update_option('md_edu_key', $key);
				update_option('md-check-del', $del ? 1 : 0);
				update_option('md-status', $status ? 1 : 0);
				update_option('md-cloud-link', $data['cloud_link']);
			}

			(($data != 'Complete push up.') && ($data != 'Path already exists.')) ? wp_send_json_success($data) : '';
		}
	}
}
add_action('wp_ajax_verify_cloud', 'verify_cloud');
add_action('wp_ajax_nopriv_verify_cloud', 'verify_cloud');
function verify_cloud()
{		
		if (isset($_POST['action']) && $_POST['action'] === 'verify_cloud') {
			$del = isset($_POST["md-check-del"]) ? true : ((get_option('md-check-del') == 1) ? true : false);
			$status = isset($_POST["md-status"]) ? true : false;
			$request_args = array(
				'method' => 'POST',
				'headers' => array(
					'Content-Type' => 'application/json'
				),
				'body' => json_encode(array(
					'token' => $_POST['token'],
					'action' => 'verify_cloud',
					'domain' => $_SERVER['HTTP_HOST']
				)),
			);
			$response = wp_remote_request(API_URL, $request_args);

			if (is_wp_error($response)) {
				$error_message = $response->get_error_message();
				wp_send_json_error("Error: " . $error_message);
			} else {
				$response_body = wp_remote_retrieve_body($response);
				$data = json_decode($response_body, true);
				//var_dump($data);die;
				if (isset($data['notification']) && $data['notification'] == 'Successful authentication') {
					update_option('md_edu_key', $data['key']);
					update_option('md-check-del', $del ? 1 : 0);
					update_option('md-status', $status ? 1 : 0);
					update_option('md-cloud-link', $data['cloud_link']);
				} else {
					wp_send_json_success($data);
				}
			}
		}
}


add_action('wp_ajax_sign_up_cloud', 'sign_up_cloud');
add_action('wp_ajax_nopriv_sign_up_cloud', 'sign_up_cloud');
function sign_up_cloud()
{	
	if (isset($_POST['action']) && $_POST['action'] === 'sign_up_cloud') {
		$action = isset($_POST["action"]) ? $_POST["action"] : "";
		$domain = $_SERVER['HTTP_HOST'];
		// POST
		$method = 'POST';
		$request_args = array(
			'method' => $method,
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => array(
				'action' => $action,
				'domain' => $domain,
				'data' => $_POST
			),
		);

		$request_args['body'] = ($method === 'POST') ? json_encode($request_args['body']) : $request_args['body'];
		$response = wp_remote_request(API_URL, $request_args);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			//sign_up_cloud();
			 			wp_send_json_error("Error: " . $error_message);

		} else {
			$response_body = wp_remote_retrieve_body($response);
			$data = json_decode($response_body, true);
			//var_dump($data);die;
			if ($data == 'Email sent successfully, check your inbox!') {
				wp_send_json_success($data);
			} else {
				wp_send_json_error("Error: " . $data);
			}

		}
	}
}

add_action('wp_ajax_forgot_cloud', 'forgot_cloud');
add_action('wp_ajax_nopriv_forgot_cloud', 'forgot_cloud');
function forgot_cloud()
{	
	if (isset($_POST['action']) && $_POST['action'] === 'forgot_cloud') {
		$action = isset($_POST["action"]) ? $_POST["action"] : "";
		$domain = $_SERVER['HTTP_HOST'];
		// POST
		$method = 'POST';
		$request_args = array(
			'method' => $method,
			'headers' => array(
				'Content-Type' => 'application/json'
			),
			'body' => json_encode(array(
				'action' => $action,
				'domain' => $domain,
				'data' => $_POST
			)),
		);

		$response = wp_remote_request(API_URL, $request_args);

		if (is_wp_error($response)) {
			$error_message = $response->get_error_message();
			 			wp_send_json_error("Error: " . $error_message);

		} else {
			$response_body = wp_remote_retrieve_body($response);
			$data = json_decode($response_body, true);
			//var_dump($data);die;
			if ($data == 'Email sent successfully, check your inbox!') {
				wp_send_json_success($data);
			} else {
				wp_send_json_error("Error: " . $data);
			}

		}
	}
}

if (get_option('md-status') == 1) {
	$UM_class = new UploadMedia();
	add_filter('wp_calculate_image_srcset', array($UM_class, 'mc_custom_image_srcset'), 10, 5);

	add_action('wp_ajax_upload_files', array($UM_class, 'upload_files'), 10, 2);
	add_action('wp_ajax_nopriv_upload_files', array($UM_class, 'upload_files'), 10, 2);
	add_filter('wp_handle_upload', array($UM_class, 'upload_files'), 10, 2);

	add_filter('wp_get_attachment_url', array($UM_class, 'mc_custom_attachment_url'), 10, 2);

	add_action('wp_ajax_get_file_media_cloud', array($UM_class, 'get_file_media_cloud'));
	add_action('wp_ajax_nopriv_get_file_media_cloud', array($UM_class, 'get_file_media_cloud'));

	add_action('wp_ajax_post_media_function', array($UM_class, 'post_media_function'));
	add_action('wp_ajax_nopriv_post_media_function', array($UM_class, 'post_media_function'));

	add_action('wp_ajax_delete_media_cloud', array($UM_class, 'delete_media_cloud'));
	add_action('wp_ajax_nopriv_delete_media_cloud', array($UM_class, 'delete_media_cloud'));

	add_action('wp_ajax_recover_cloud', array($UM_class, 'recover_cloud'));
	add_action('wp_ajax_nopriv_recover_cloud', array($UM_class, 'recover_cloud'));
	
	add_action('wp_ajax_delete_file_cloud', array($UM_class, 'delete_file_cloud'));
	add_action('wp_ajax_nopriv_delete_file_cloud', array($UM_class, 'delete_file_cloud'));
	
}

class UploadMedia
{
	
	public function delete_file_cloud()
	{
		if (isset($_POST['action']) && $_POST['action'] === 'delete_file_cloud') {
			$filename = '';
			check_key_ajax_function($filename , null, '', false, true);
		}
	}
	
	public function mc_custom_image_srcset($sources, $size_array, $image_src, $image_meta, $attachment_id)
	{	
		foreach ($sources as $source => &$src) {
			$cloudLink = get_option('md-cloud-link');
			if (strpos(($sources[$source]['url']), $cloudLink) !== false) {
				$pattern1 = "/^https:\/\/[^\/]+\/wp-content\/uploads\//";
				$cleanUrl1 = preg_replace($pattern1, "", $sources[$source]['url']);
				if (filter_var($cleanUrl1)) {
					$sources[$source]['url'] = $cleanUrl1;
				};
			};
		};
    	return $sources;
	}

	public function download_file($url, $savePath)
	{
		if(!file_exists($savePath)) {
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
			curl_close($ch);

			$file = fopen($savePath, 'w');
			fwrite($file, $data);
			fclose($file);
		}

	}

	public function upload_files($path = '', $post_file_new = true)
	{
		$upload_info = wp_upload_dir();
		$upload_path = $upload_info['path'];
		$upload_url = $upload_info['url'];
		$base_url = $upload_info['baseurl'];
		$base_path = $upload_info['basedir'];

		if ($post_file_new) {
			$file_name = $this->get_file_name();
			$path = $upload_path . "/" . $file_name;
			$url_upload = $upload_url . "/" . $file_name;
			$year = date("Y");
			$month = date("m");
		} else {

			$file_name = basename($path);
			$parts = explode('/', $path);
			$last_part = array_slice($parts, -3);
			$year = $last_part[0];
			$month = $last_part[1];
			// ngày tháng cũ
			$url_upload = $base_url . '/' . $year . '/' . $month . '/' . $file_name;
		}
		$domain = $_SERVER['HTTP_HOST'];
		$date = $year . '/' . $month . '/';
		$DoDate = $domain . '/' .  $year . '/' . $month . '/';
		if (!preg_match('/\d{4}\/\d{2}/', $path)) {
			$DoDate = $domain . '/';
			$date = ''; 
			$url_upload = $base_url . '/' . $file_name;
		}
		$filename = $DoDate . $file_name;

		$dell = get_option('md-check-del');
		$cloudLink = get_option('md-cloud-link');
		$url = $cloudLink . '/' . $filename;
		//push data to the cloud
		$fileInfo = [
			'name' => basename($path),
			'size' => filesize($path),
			'modified' => date("Y-m-d H:i:s", filemtime($path)),
			'url' => $url
		];
		check_key_ajax_function($filename, $url_upload, $fileInfo);
		//with the added path the editing function can be developed later if needed
		$file_type = wp_check_filetype($path);

		if ($post_file_new) {
			$attachment = array(
				'guid' => $url,
				'post_mime_type' => mime_content_type($path),
				'post_title' => pathinfo($path, PATHINFO_FILENAME),
				'post_content' => '',
				'post_status' => 'inherit'
			);
			// 		if(strpos($file_type['type'], 'image/') === 0) {
			// 			$attachment_id = wp_insert_attachment($attachment, $path);
			// 		}	else {
			// 			$attachment_id = wp_insert_attachment($attachment, $url);
			// 		};
			$attachment_id = wp_insert_attachment($attachment, $path);
			if (!is_wp_error($attachment_id)) {
				$attachment_data = array(
					'ID' => $attachment_id,
					'post_mime_type' => mime_content_type($path),
					'guid' => $url,

				);
				//wp_update_post($attachment_data);

				if (strpos($file_type['type'], 'image/') === 0) {
					//Add attachment of version images
					require_once ABSPATH . 'wp-admin/includes/image.php';
					$attachmentdata = wp_generate_attachment_metadata($attachment_id, $path);
					if (!is_wp_error($attachmentdata)) {
						foreach ($attachmentdata['sizes'] as $size) {
							$file_meta = $size['file'];
							$meta_path = $upload_path . "/" . $file_meta;
							$meta_url = $upload_url . "/" . $file_meta;
							$meta_name = $DoDate . $file_meta;
							$cloudLink = get_option('md-cloud-link');
							$url_b2 = $cloudLink . '/' . $meta_name;
							$fileInfo = [
								'name' => basename($meta_path),
								'size' => filesize($meta_path),
								'modified' => date("Y-m-d H:i:s", filemtime($meta_path)),
								'url' => $url_b2
							];
							check_key_ajax_function($meta_name, $meta_url, $fileInfo);
							($dell == 1 && file_exists($meta_path)) ? unlink($meta_path) : null;
						};

						$attachmentdata['file'] = $url;
					};

					wp_update_attachment_metadata($attachment_id, $attachmentdata);
				} else {
					wp_update_attachment_metadata($attachment_id, wp_generate_attachment_metadata($attachment_id, $path));
				}

				$result = wp_prepare_attachment_for_js($attachment_id);
				($dell == 1 && file_exists($path)) ? unlink($path) : null;
				wp_send_json_success($result);
			} else {
				return new WP_Error('attachment_error', 'Failed to create attachment in the database.');
			}
		} else {
			global $wpdb;
			$post_id = $wpdb->get_var(
				$wpdb->prepare(
					"
										SELECT ID
											FROM {$wpdb->posts}
											WHERE post_type = %s AND guid LIKE %s
											",
					'attachment',
					'%' . $wpdb->esc_like(basename($path)) . '%'
				)
			);

			if ($post_id) {
				$result = $wpdb->update(
					$wpdb->posts,
					array('guid' => $url),
					array('ID' => intval($post_id)),
					array('%s'),
					array('%d')
				);
				if (strpos($file_type['type'], 'image/') === 0) {
					$meta_value = get_post_meta($post_id, '_wp_attachment_metadata', true);

					if ($meta_value) {
						foreach ($meta_value['sizes'] as $size) {
							$file_meta = $size['file'];
							$meta_path = $base_path . "/". $date . $file_meta;
							$meta_url = $base_url . "/" . $date . $file_meta;
							$meta_name = $DoDate . $file_meta;
							$cloudLink = get_option('md-cloud-link');
							$url_b2 = $cloudLink . '/' . $meta_name;
							$fileInfo = [
								'name' => basename($meta_path),
								'size' => $size['filesize'],
								'modified' => '',
								'url' => $url_b2
							];
							check_key_ajax_function($meta_name, $meta_url, $fileInfo);
							($dell == 1 && file_exists($meta_path)) ? unlink($meta_path) : null;
						};

						$meta_value['file'] = $url;

						wp_update_attachment_metadata($post_id, $meta_value);


						($dell == 1 && file_exists($path)) ? unlink($path) : null;
					} else {
						// Xử lý trường hợp không tìm thấy giá trị meta_value hoặc có lỗi xảy ra
						wp_send_json_error('The meta_value value was not found or an error occurred.');
					}
				}
			} else {
				wp_send_json_error('Invalid database');
			}
			return true;
		}
	}


	public function get_file_name()
	{
		$upload_dir = wp_get_upload_dir();
		$upload_path = $upload_dir['path'];
		$files = scandir($upload_path);
		$files = array_diff($files, ['.', '..']);

		usort($files, function ($a, $b) use ($upload_path) {
			return filemtime($upload_path . '/' . $b) - filemtime($upload_path . '/' . $a);
		});

		$newest_file = $files[0];
		return $newest_file;
	}

	public function mc_custom_attachment_url($url, $post_id)
	{
		$cloudLink = get_option('md-cloud-link');
		$guid = get_the_guid($post_id);
		if (strpos($guid, $cloudLink) !== false) {
			$pattern = "/^https:\/\/[^\/]+\/wp-content\/uploads\//";
			$domain = $_SERVER['HTTP_HOST'];
			$cleanUrl = $cloudLink. '/' . $domain . '/' . preg_replace($pattern, "", $url);
			if (filter_var($cleanUrl)) {
				return $cleanUrl;
			};
		};

		return $url;
	}
	
	function getFilesRecursive($folder) {
		$files = [];
		$iterator = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST,
			RecursiveIteratorIterator::CATCH_GET_CHILD
		);

		foreach ($iterator as $file) {
			if ($file->isFile()) {
				$files[] = $file->getPathname();
			}
		}

		return $files;
	}

	public function get_file_media_cloud()
	{
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		if (isset($_POST['action']) && $_POST['action'] === 'get_file_media_cloud') {
			$file_upload = [];
			$upload_dir = wp_get_upload_dir();
			$path_folder = $upload_dir['basedir'];
			$base_url = $upload_dir['baseurl'];
			$files = $this->getFilesRecursive($path_folder);
			
			if ($files !== false) {
				foreach ($files as $file) {
					global $wpdb;
					$id = $wpdb->get_var(
						$wpdb->prepare(
							"
										SELECT ID
											FROM {$wpdb->posts}
											WHERE post_type = %s AND guid LIKE %s
											",
							'attachment',
							'%' . $wpdb->esc_like(basename($file)) . '%'
						)
					);
					if (is_file($file) && $id) {
						$cloudLink = get_option('md-cloud-link');
						$guid = get_the_guid($id);
						if (strpos($guid, $cloudLink) === false) {
							$file_upload[] = $file;
						};
					} else if (is_file($file)) {
						$results = $wpdb->get_results(
							$wpdb->prepare(
								"
										SELECT post_id, meta_value
										FROM {$wpdb->postmeta}
										WHERE meta_key = %s AND meta_value LIKE %s
										",
								'_wp_attached_file',
								'%' . $wpdb->esc_like(basename($file)) . '%'
							)
						);
						if ($results) {
							$post_id = $results[0]->post_id;
							$meta_value = $results[0]->meta_value;
							$saveUrl = $base_url . '/' . $meta_value;
							$result = $wpdb->update(
								$wpdb->posts,
								array('guid' => $saveUrl),
								array('ID' => intval($post_id)),
								array('%s'),
								array('%d')
							);
							$file_upload[] = $file;
						}
					}
				};
			}		
			if ($file_upload) {
				wp_send_json_success($file_upload);
			} else {
				wp_send_json_error($file_upload);
			};
		};
	}


	//post media upload
	public function post_media_function()
	{
		date_default_timezone_set('Asia/Ho_Chi_Minh');

		if (isset($_POST['action']) && $_POST['action'] === 'post_media_function') {
			//var_dump($_POST);die;
			$file = $_POST['path'];

			$status = $this->upload_files($file, false);
			if ($status) {

				$all_file = "
					<div class='d-flex align-items-center justify-content-between get-image'>
						<p class='my-2'>'$file'</p> 
						<i class='fs-2 bi bi-check-lg text-success'></i>
					</div>";
			} else {
				$all_file = null;
				// echo "No post found with the given meta value.";
			};
			wp_send_json_success(@$all_file);
		};
	}




	//Recover upload

	public function recover_cloud()
	{	
		$startTime = microtime(true);
    	$maxExecutionTime = 30;
		
		if (isset($_POST['action']) && $_POST['action'] === 'recover_cloud') {
			// $link = $_POST['href'];
			global $wpdb;
			$domain = $_SERVER['HTTP_HOST'];
			$cloudLink = get_option('md-cloud-link');
			$url_cloud = $cloudLink .'/' . $domain;
			$posts_id = $wpdb->get_col(
				$wpdb->prepare(
					"
										SELECT ID
											FROM {$wpdb->posts}
											WHERE post_type = %s AND guid LIKE %s
											",
					'attachment',
					'%' . $wpdb->esc_like($url_cloud) . '%'
				)
			);
			$upload_info = wp_upload_dir();
			$base_url = $upload_info['baseurl'];
			$base_path = $upload_info['basedir'];
			
			foreach ($posts_id as $post_id) {
				$currentTime = microtime(true);
    			$executionTime = $currentTime - $startTime;
				//Interrupt request
				if ($executionTime >= $maxExecutionTime) {
					wp_send_json_success('Start_again');
				}
				$guid = get_the_guid($post_id);
				$domain = $_SERVER['HTTP_HOST'];
				$cloudLink = get_option('md-cloud-link');
				$url_cloud = $cloudLink .'/' . $domain;

				$file_location = str_replace($url_cloud, '', $guid);
				$savePath = $base_path . $file_location;
				$saveUrl = $base_url . $file_location;

				$this->download_file($guid, $savePath);
				$file_type = wp_check_filetype($savePath);
				if ($post_id) {
					$result = $wpdb->update(
						$wpdb->posts,
						array('guid' => $saveUrl),
						array('ID' => intval($post_id)),
						array('%s'),
						array('%d')
					);
					if (strpos($file_type['type'], 'image/') === 0) {
						$meta_value = get_post_meta($post_id, '_wp_attachment_metadata', true);
						$metaBasePath =  str_replace(basename($savePath), '', $savePath);
						$date = substr($metaBasePath, -9);
						if ($meta_value) {
							foreach ($meta_value['sizes'] as $size) {
								$meta_name = $size['file'];
								$meta_path = $metaBasePath . $meta_name;
								$cloudLink = get_option('md-cloud-link');
								$url_b2 = $cloudLink . '/' .$domain . $date . $meta_name;
								$this->download_file($url_b2, $meta_path);
							};

							$meta_value['file'] = $file_location;

							wp_update_attachment_metadata($post_id, $meta_value);
						} else {
							wp_send_json_error('The meta_value value was not found or an error occurred.');
						}
					}
				} else {
					wp_send_json_error('Invalid database');
				}
			}
			wp_send_json_success(true);
		};
	}
	// Delete file

	public function delete_media_cloud()
	{
		if (isset($_POST['action']) && $_POST['action'] === 'delete_media_cloud') {
			global $wpdb;
			$domain = $_SERVER['HTTP_HOST'];
			$cloudLink = get_option('md-cloud-link');
			$url_cloud = $cloudLink . '/' . $domain;
			$posts_id = $wpdb->get_col(
				$wpdb->prepare(
					"
										SELECT ID
											FROM {$wpdb->posts}
											WHERE post_type = %s AND guid LIKE %s
											",
					'attachment',
					'%' . $wpdb->esc_like($url_cloud) . '%'
				)
			);
			$upload_info = wp_upload_dir();
			$base_url = $upload_info['baseurl'];
			$base_path = $upload_info['basedir'];
			foreach ($posts_id as $post_id) {
				$guid = get_the_guid($post_id);
				$domain = $_SERVER['HTTP_HOST'];
				$cloudLink = get_option('md-cloud-link');
				$url_cloud = $cloudLink . '/' . $domain;

				$file_location = str_replace($url_cloud, '', $guid);
				$delPath = $base_path . $file_location;
				//$delUrl = $base_url . $file_location;
				$file_type = wp_check_filetype($delPath);
				$this->deleteFile($delPath);
				if ($post_id) {
					if (strpos($file_type['type'], 'image/') === 0) {
						$meta_value = get_post_meta($post_id, '_wp_attachment_metadata', true);
						$metaBasePath =  str_replace(basename($delPath), '', $delPath);
						$date = substr($metaBasePath, -9);
						if ($meta_value) {
							foreach ($meta_value['sizes'] as $size) {
								$meta_path = $metaBasePath . $size['file'];
								$this->deleteFile($meta_path);
							};
						} else {
							wp_send_json_error('The meta_value value was not found or an error occurred.');
						}
					}
				} else {
					wp_send_json_error('Invalid database');
				}
			}
			wp_send_json_success(true);
		};
	}
	

	
	public function deleteFile($filePath)
	{
		if (file_exists($filePath)) {
			if (unlink($filePath)) {
				// echo "File deleted successfully.";
			} else {
				// echo "Unable to delete the file.";
			}
		} else {
			// echo "File does not exist.";
		}
	}
}
