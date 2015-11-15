<?php
/**
*
* Visit counter by forum extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 ErnadoO <http://www.phpbb-services.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace ernadoo\visitcounterbyforum\event;

/**
 * Event listener
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\user */
	protected $user;

	/**
	* Constructor
	*
	* @param \phpbb\user							$user			User object
	* @access public
	*/
	public function __construct(\phpbb\user $user)
	{
		$this->user 		= $user;
	}

	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return	array
	* @static
	* @access	public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.display_forums_modify_template_vars'  => 'assign_templates_vars',
			'core.user_setup'							=> 'load_language_on_setup',
		);
	}

	/**
	* Modify the template data block
	*
	* @return null
	*/
	public function assign_templates_vars($event)
	{
		$row = $event['row'];

		if ($row['forum_type'] != FORUM_LINK)
		{
			$online_users = obtain_users_online($row['forum_id']);

			$forum_row = $event['forum_row'];

			$forum_row = array_merge($forum_row, array(
				'L_FORUM_VISIT'	=> $this->user->lang('FORUM_VISITS', $online_users['total_online']),
			));
			$event['forum_row'] = $forum_row;
		}
	}

	/**
	* Load language files during user setup
	*
	* @param	object $event The event object
	* @return	null
	*/
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'ernadoo/visitcounterbyforum',
			'lang_set' => 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}
}
