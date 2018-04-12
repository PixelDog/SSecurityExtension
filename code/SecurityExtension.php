<?php


/**
 * SecurityExtension is a class restricting ADMIN access by IP address
 * and checking that users belong to a specific subsite (if using subsites)
 *
 * @package    SiteAdmin
 * @category   Security
 * @version    1.0
 */
class SecurityExtension extends Extension {


	// whitelist of allowed IP addresses
	const ALLOWED = [
		"127.0.0.1",
		// add IP's here
	];

    /**
     * Get the client IP
     *
     * @return String 
     * @access private
     */
	private function getClientIp() {

		if ( getenv( 'HTTP_CLIENT_IP' ) )
			$ip_address = getenv( 'HTTP_CLIENT_IP' );
		else if( getenv( 'HTTP_X_FORWARDED_FOR' ) )
			$ip_address = getenv( 'HTTP_X_FORWARDED_FOR' );
		else if( getenv( 'HTTP_X_FORWARDED' ) )
			$ip_address = getenv( 'HTTP_X_FORWARDED' );
		else if( getenv( 'HTTP_FORWARDED_FOR' ) )
			$ip_address = getenv( 'HTTP_FORWARDED_FOR' );
		else if( getenv( 'HTTP_FORWARDED' ) )
		   $ip_address = getenv( 'HTTP_FORWARDED' );
		else if(getenv( 'REMOTE_ADDR') )
			$ip_address = getenv( 'REMOTE_ADDR' );
		else
			$ip_address = 'UNKNOWN';
		return $ip_address;
	}


    /**
     * Check user permissions and IP before initializing the controller 
     *
     * @return Controller action 
     * @access public
     */
	public function onBeforeInit(){
		
		// Check user permissions
		$user_is_admin = $this->siteAdmin();
		if( $user_is_admin ){
		
			// get the IP and compare against the whitelist
			$remote_ip = $this->getClientIp();
			if( !in_array( $remote_ip, self::ALLOWED ) ){
			
				//if IP's is not in the list, deny access
				return $this->foilHacker();
			}
		}
		
		$this->subsiteCheck( $user_is_admin );
	}
	

    /**
     * Logout any ADMIN users with invalid IP and redirect to login.
     *
     * @return Controller action 
     * @access private
     */
	private function foilHacker(){
	
		Security::logout(false);
		return Controller::curr()->redirect("/Security/login/?_c=1001");
	}
	

    /**
     * Check to see if the user has ADMIN privilidges
     *
     * @return Boolean 
     * @access private
     */	
	private function siteAdmin() {
	
		if( Permission::check('ADMIN') ) {
			return true;
		}
		return false;
	}


    /**
     * Check for subsites and be sure the user belongs to the site
     *
     * @param Boolean $user_is_admin
     *
     * @return Controller action 
     * @access private
     */
    private function subsiteCheck( $user_is_admin ){
    
    	if( class_exists('Subsite') ){
    	
			$current_subsite_id = Subsite::current_subsite_id();
			$member =  Member::currentUser();
			$member_subsite_id = $member->SubsiteID; 

			/**
     		 * Users that are members of a subsite will have a SubsiteID > 0
     		 * User's with ADMIN rights get a pass
     		 */
			if(	$member_subsite_id > 0 &&
				$current_subsite_id != $member_subsite_id &&
				!$user_is_admin ) {
					$this->foilHacker();

			}
		}
    }
} 
