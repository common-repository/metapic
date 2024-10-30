<?php

use MetaPic\ApiClient;

class WP_MTPC extends stdClass {
	use WP_MTPC_Utilities;
	
	private $api_url = 'https://api.metapic.se';
	private $userapi_url = 'https://mtpc.se';
	private $cdn_url = 'https://api.metapic.se';
	private $plugin_dir;
	private $plugin_url;
	
	/* @var ApiClient $client */
	private $client;
	
	/* @var WP_MTPC_Account $client */
	private $account;
	
	/* @var WP_MTPC_Settings $client */
	private $settings;
	
	/* @var WP_MTPC_Menus $client */
	private $menus;
	
	/* @var WP_MTPC_Notices $client */
	private $notices;
	
	private $templateVars = [];
	private $debugMode;
	private $isDocker;
	private $accessKey = 'metapic_access_token';
	private $tokenUrl;
	private $autoRegister = false;
	private $userBelongsToBlog = true;
	private $activeAccount;
	private $forceSSL = true;
	
	public function __construct( $plugin_dir, $plugin_url ) {
		$this->debugMode = ( ( defined( 'MTPC_DEBUG' ) && MTPC_DEBUG === true )
		                     || ( isset( $_ENV['MTPC_DEBUG'] )
		                          && $_ENV['MTPC_DEBUG'] === 'true' ) );
		$this->isDocker  = ( isset( $_ENV['MTPC_IS_DOCKER'] )
		                     && $_ENV['MTPC_IS_DOCKER'] === 'true' );
		
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;
		
		
		if ( $this->forceSSL || is_ssl() ) {
			$this->api_url     = 'https://api.metapic.se';
			$this->userapi_url = 'https://mtpc.se';
			$this->cdn_url     = 'https://api.metapic.se';
		}
		
		if ( defined( 'MTPC_API_URL' ) && $this->debugMode ) {
			$this->api_url = MTPC_API_URL;
		}
		
		$this->client = new ApiClient( $this->getApiUrl(),
			get_site_option( 'mtpc_api_key' ),
			get_site_option( 'mtpc_secret_key' ) );
		
		$this->tokenUrl = rtrim( get_bloginfo( 'url' ), '/' ) . '/?'
		                  . $this->accessKey;
		
		$this->settings = new WP_MTPC_Settings( $this->client );
		$this->menus    = new WP_MTPC_Menus();
		$this->account  = new WP_MTPC_Account( $this->client );
		$this->notices  = new WP_MTPC_Notices();
		
		$this->notices->register_notice_messages( [
			[
				'type'    => 'mtpc-settings-saved',
				'message' => __( 'Your settings have been saved', 'metapic' ),
			],
			[
				'type'    => 'mtpc-user-register',
				'message' => __( 'Account registered', 'metapic' ),
			],
			[
				'type'    => 'mtpc-user-register-fail',
				'message' => __( 'Registration failed', 'metapic' ),
				'status'  => 'error',
			],
			[
				'type'    => 'mtpc-user-login',
				'message' => __( 'You have been logged in', 'metapic' ),
			],
			[
				'type'    => 'mtpc-user-login-fail',
				'message' => __( 'Login failed', 'metapic' ),
				'status'  => 'error',
			],
			[
				'type'    => 'mtpc-user-logout',
				'message' => __( 'You have been logged out', 'metapic' ),
			],
			[
				'type'    => 'mtpc-account-created',
				'message' => __( 'You account has been created', 'metapic' ),
			],
			[
				'type'    => 'mtpc-account-activated',
				'message' => __( 'You account has been activated', 'metapic' ),
			],
			[
				'type'    => 'mtpc-account-deactivated',
				'message' => __( 'You account has been deactivated',
					'metapic' ),
			],
			[
				'type'    => 'mtpc-account-create-failed',
				'message' => __( 'Account creation failed, please contact technical support',
					'metapic' ),
				'status'  => 'error',
			],
		] );
		
		$this->notices->register_notices();
		
		add_action( 'admin_menu', function () {
			$this->menus->register_admin_menu( $this );
		} );
		
		add_action( 'network_admin_menu', function () {
			$this->menus->register_network_admin_menu( $this );
		} );
		
		//$this->setupOptionsPage();
		//$this->setupNetworkOptions();
		$this->setupLang();
		$this->setupIframeRoutes();
		$this->setupDeeplinkWidget();
		
		if ( is_multisite() ) {
			$this->autoRegister
				                     = (bool) get_site_option( 'mtpc_registration_auto' );
			$belongs_to_blog         = get_option( 'mtpc_user_belongs_to_blog', null );
			$this->userBelongsToBlog = ( $belongs_to_blog !== false );
		}
		$this->activeAccount = $this->has_active_account();
		
		$this->settings->handle_admin_ms_settings( $this->getApiUrl() );
		$this->settings->handle_mtpc_user_my_account( $this->debugMode );
		$this->settings->handle_mtpc_user_new();
		$this->settings->handle_mtpc_user_reactivate();
		
		$this->account->handle_mtpc_user_register();
		$this->account->handle_mtpc_user_login();
		
		add_action( 'admin_init', function () {
			if ( $this->activeAccount || $this->autoRegister ) {
				if (
					! $this->activeAccount && $this->autoRegister &&
					$this->userBelongsToBlog && ! is_super_admin()
				) {
					$currentUser = wp_get_current_user();
					if ( $currentUser ) {
						$blogUsers = get_users( [ 'orderby' => 'display_name' ] );
						$match     = array_filter( $blogUsers, function ( $user ) use ( $currentUser ) {
							return $user->ID == $currentUser->ID;
						} );
						if ( count( $match ) > 0 ) {
							$this->registerCurrentUser();
						} else {
							update_option( 'mtpc_user_belongs_to_blog', false, true );
						}
					}
				}
				
				if ( $this->activeAccount ) {
					$this->setupBackendJsOptions();
				//	$this->setupHelpButton();
					$this->setupDeeplinkWidget();
					$this->setupDeeplinkPublishing();
				}
			}
		} );
		
		$this->setupFrontendJsOptions();
		add_filter( 'wp_kses_allowed_html', function ( $tags ) {
			foreach ( $tags as $key => $value ) {
				$tags[ $key ]['data-metapic-id']       = 1;
				$tags[ $key ]['data-metapic-tags']     = 1;
				$tags[ $key ]['data-metapic-link-url'] = 1;
			}
			
			return $tags;
		}, 500, 1 );

		// Commercial message
        if(is_admin() == false) {
            if(is_multisite()) {
                $commercial_message_option = get_site_option('mtpc_commercial_interest_message');
            } else {
                $commercial_message_option = get_option('mtpc_commercial_interest_message');
            }

            if($commercial_message_option) {
                function commercial_message_display($content) {
                    if(strpos($content, 'mtpc-container') !== false ||
                        strpos($content, 'mtpc-collage') !== false ||
                        strpos($content, 'https://c.mtpc.se') !== false
                    ) {
                        $message_text = __('This post has affiliate links', 'metapic');
                        $message = '<div class="mtpc_commercial_message"><div>'. $message_text .'</div></div>';
                        $content = $message . $content;
                    }

                    return $content;
                }
                add_filter('the_content', 'commercial_message_display');
            }
        }
	}
	
	public function activate() {
		if ( is_multisite() ) {
			add_site_option( 'mtpc_deeplink_auto_default', true );
			add_site_option( 'mtpc_registration_auto', false );
            add_site_option( 'mtpc_commercial_interest_message', true );
		} else {
			add_option( 'mtpc_deeplink_auto_default', true );
            add_option( 'mtpc_commercial_interest_message', false );
		}
	}
	
	private function setupFrontendJsOptions() {
		$jsHandle = 'mtpc_frontend_js';
		
		add_action( 'admin_head', function () {
			wp_enqueue_style( 'metapic_base_css',
				$this->plugin_url . '/css/metapic.css' );
		} );
		
		add_action( 'wp_head', function () use ( $jsHandle ) {
			if ( $this->debugMode ) {
				$cdn = get_option( 'mtpc_cdn_uri_string' );
				if ( $cdn ) {
					wp_enqueue_script( $jsHandle,
						$cdn
						. '/metapic.lasyloading.min.js', [ 'jquery' ],
						false, true );
					wp_enqueue_style( 'mtpc_frontend_css',
						$cdn
						. '/metapic.preLogin.css' );
				} else {
					$aws_url = 'https://s3-eu-west-1.amazonaws.com';
					wp_enqueue_script( $jsHandle, $aws_url
					                              . '/metapic-cdn/dev/metapic.lasyloading.min.js',
						[ 'jquery' ], false, true );
					wp_enqueue_style( 'mtpc_frontend_css', $aws_url
					                                       . '/metapic-cdn/site/css/remote/metapic.min.css' );
				}
			} else {
				
				$aws_url = 'https://s3-eu-west-1.amazonaws.com';

				wp_enqueue_script( $jsHandle,
					$aws_url . '/metapic-cdn/dev/metapic.lasyloading.min.js',
					[ 'jquery' ], false, true );
				wp_enqueue_style( 'mtpc_frontend_css',
					$aws_url . '/metapic-cdn/site/css/remote/metapic.min.css' );
			}

            wp_enqueue_style( 'mtpc_frontend_local_css', $this->plugin_url .'/css/metapic_frontend.css' );
		}, 10 );
		
		add_action( 'wp_footer', function () {
			$this->getTemplate( 'metapic-load' );
		} );
	}
	
	private function setupBackendJsOptions() {
		$baseUrl         = $this->isDocker ? 'http://localhost:3000'
			: $this->client->getBaseUrl();
		$accessToken     = get_option( 'mtpc_access_token' );
		$mce_plugin_name = 'metapic';
		
		// Declare script for new button
		
		add_filter( 'mce_external_plugins',
			function ( $plugin_array ) use ( $mce_plugin_name ) {
				$plugin_array[ $mce_plugin_name ] = $this->plugin_url
				                                    . '/js/metapic.js';
				
				return $plugin_array;
			} );
		
		add_filter( 'wp_mce_translation', function ( $mce_translation ) {
			$translations = [
				'Link content',
				'Metapic link',
				'Tag image',
				'Metapic image',
				'Add collage',
				'Metapic collage',
			];
			foreach ( $translations as $translation ) {
				$mce_translation[ $translation ] = __( $translation,
					'metapic' );
			}
			
			return $mce_translation;
		}, 10 );
		
		add_filter( 'tiny_mce_before_init', function ( $mceInit ) {
			$mceInit['mtpc_iframe_url'] = $this->tokenUrl;
			$mceInit['mtpc_plugin_url'] = $this->plugin_url;
			if ( $this->isDocker ) {
				$mceInit['mtpc_base_url'] = 'http://localhost:3000';
			} else {
				$mceInit['mtpc_base_url'] = $this->client->getBaseUrl();
			}
			$mceInit['mtpc_access_token'] = get_option( 'mtpc_access_token' );
			$mceInit['mtpc_show_collage_image_taging'] = get_site_option( 'mtpc_show_collage_image_taging', true );

			return $mceInit;
		}, 500, 1 );
		
		// Register new button in the editor
		add_filter( 'mce_buttons',
			function ( $buttons ) use ( $mce_plugin_name ) {
				$buttons[] = $mce_plugin_name . 'link';
				$buttons[] = $mce_plugin_name . 'img';
				$buttons[] = $mce_plugin_name . 'collage';
				
				return $buttons;
			} );
		
		add_filter( 'mce_css', function ( $styles ) {
			$styles .= ',' . $this->plugin_url . '/css/metapic.css';
			
			return $styles;
		} );
		
		add_action( 'admin_enqueue_scripts',
			function () use ( $baseUrl, $accessToken ) {
				$current_screen = get_current_screen();
				$is_gutenberg   = ( method_exists( $current_screen,
						'is_block_editor' )
				                    && $current_screen->is_block_editor() );
				$api_url        = $this->isDocker ? 'http://localhost:3000'
					: $this->getApiUrl();
				
				wp_enqueue_script( 'iframeScript',
					$api_url . '/javascript/iframeScript.js', [],
					'1.1.2', true );
				
				wp_enqueue_script( 'metapicAdmin',
					$this->plugin_url . '/js/metapic-admin.js', [ 'jquery' ],
					'1.1.2' );
				
				wp_localize_script( 'metapicAdmin', 'wp_mtpc', [
					'baseUrl'     => $baseUrl,
					'accessToken' => $accessToken,
					'isGutenberg' => $is_gutenberg ? 'true' : 'false',
					'wpVersion' => get_bloginfo( 'version' ),
				] );
				
				wp_enqueue_style( 'metapic_admin_css',
					$this->plugin_url . '/css/metapic.css' );
			} );
		
		add_action( 'wp_ajax_mtpc_deeplink', function () {
			$accessToken = get_option( 'mtpc_access_token' );
			$userId      = get_option( 'mtpc_id' );
			$newContent  = $this->client->createDeepLinks( $userId,
				$_POST['links'], $accessToken );
			
			wp_send_json( [
				'user_id' => $userId,
				'token'   => $accessToken,
				'content' => $newContent,
			] );
		} );

	}
	
	private function setupHelpButton() {
		add_action( 'media_buttons', function () {
			$this->getTemplate( 'help-button' );
		} );
	}
	
	private function setupLang() {
		add_action( 'plugins_loaded', function () {
			$langPath = basename( $this->plugin_dir ) . '/languages/';
			load_plugin_textdomain( 'metapic', false, $langPath );
		} );
		
		add_filter( 'mce_external_languages', function ( $locales ) {
			$ds                  = DIRECTORY_SEPARATOR;
			$path                = $this->plugin_dir . $ds . 'tinymce-lang'
			                       . $ds . 'metapic-langs.php';
			$locales ['metapic'] = $path;
			
			return $locales;
		} );
	}
	
	/**
	 * @return ApiClient
	 */
	public function getClient() {
		return $this->client;
	}
	
	private function getApiUrl() {
		$url = false;
		if ( $this->debugMode ) {
			$url = is_multisite() ? get_site_option( 'mtpc_api_url' )
				: get_option( 'mtpc_uri_string', '' );
		}
		
		return $url ? $url : $this->api_url;
	}
	
	public function getTemplate( $templateName, array $templateVars = [] ) {
		global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;
		
		if ( is_array( $wp_query->query_vars ) ) {
			extract( $wp_query->query_vars, EXTR_SKIP );
		}
		extract( $this->templateVars, EXTR_OVERWRITE );
		extract( $templateVars, EXTR_OVERWRITE );
		require $this->plugin_dir . "/templates/{$templateName}.php";
	}
	
	public function __get( $var ) {
		return @$this->templateVars[ $var ];
	}
	
	public function __set( $var, $value ) {
		$this->templateVars[ $var ] = $value;
	}
	
	public function __isset( $var ) {
		return isset($this->templateVars[ $var ]);
	}
	
	private function setupIframeRoutes() {
		add_action( 'init', function () {
			add_rewrite_rule( 'hello.php$', 'index.php?' . $this->accessKey,
				'top' );
		}
		);
		
		add_filter( 'query_vars', function ( $query_vars ) {
			$query_vars[] = $this->accessKey;
			
			return $query_vars;
		}
		);
		
		add_action( 'parse_request', function ( $wp ) {
			if ( array_key_exists( $this->accessKey, $wp->query_vars ) ) {
				$accessToken = get_option( 'mtpc_access_token' );
				
				if ( $this->autoRegister && ! $this->activeAccount ) {
					$user        = $this->registerCurrentUser();
					$accessToken = $user['access_token']['access_token'];
				}
				wp_send_json( [
					'access_token' => [ 'access_token' => $accessToken ],
					'metapicApi'   => $this->client->getBaseUrl(),
				] );
			}
		} );
	}
	
	private function registerCurrentUser() {
		$wp_user = wp_get_current_user();
		$user    = $this->client->activateUser( $wp_user->user_email );
		if ( $user['access_token'] === null ) {
			$this->client->createUser( [
				'email'    => $wp_user->user_email,
				'username' => $wp_user->user_login,
			] );
			$user = $this->client->activateUser( $wp_user->user_email );
		}
		$this->activate_account( $user['id'], $wp_user->user_email,
			$user['access_token']['access_token'] );
		$this->activeAccount = true;
		
		return $user;
	}
	

	private function setupDeeplinkWidget() {
		$accesstoken = get_option( 'mtpc_access_token' );
		if ($accesstoken != "" && $accesstoken != NULL) {
			add_action( 'wp_dashboard_setup', function () {
				wp_add_dashboard_widget(
					'metapic-deeplink-widget',         // Widget slug.
					__( 'Create Your metapic Link', 'metapic' ),         // Title.
					function () {
						$this->getTemplate( 'widgets/Deeplinking' );
					}
				);
			} );
		}
	}
	

	
	private function setupDeeplinkPublishing() {
		add_action( 'post_submitbox_misc_actions', function ( $post ) {
			$this->getTemplate( 'deeplink-publish' );
		} );
		
		add_action( 'save_post', function ( $postId ) {
			if ( isset( $_POST['mtpc_deeplink_auto'] ) ) {
				update_post_meta( $postId, 'mtpc_deeplink_auto',
					(int) $_POST['mtpc_deeplink_auto'] );
			}
		} );
	}
	
	public function is_edit_page( $new_edit = null ) {
		global $pagenow;
		
		if ( ! is_admin() ) {
			return false;
		}
		
		if ( $new_edit == "edit" ) {
			return in_array( $pagenow, [ 'post.php', ] );
		} elseif ( $new_edit == "new" ) {
			return in_array( $pagenow, [ 'post-new.php' ] );
		} else {
			return in_array( $pagenow, [ 'post.php', 'post-new.php' ] );
		}
	}
	
}
