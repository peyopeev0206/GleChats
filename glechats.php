<?php
/**
 * GleChats: send messages in Google Chats channels by posting to their webhooks
 *
 * @author Peyo Peev
 * @link https://github.com/peyopeev0206/GleChats
 * @license http://opensource.org/licenses/LGPL-3.0 GNU Lesser General Public License, version 3.0
 */
class glechats
{
	/**
	* Webhook to post to
	*/
	protected static $webhook = '';

	/**
	 * Defult params
	 */
	private static $defultParams = array(
		'title' => '',
		'headerTitle' => '',
		'headerImageURL' => '',
		'sections' => array(
			'text' => ''
		),
		'button' => array(
			'text' => '',
			'link' => ''
		)
	);

	/**
	 * Constructor: Set up the Google Chat webhook
	 * @param string $webhook Google Chat webhook
	 * @throws InvalidArgumentException
	 */
	public function __construct($webhook)
	{
		if (empty($webhook = trim($webhook)))
		{
			throw new \InvalidArgumentException(
				'Empty webhook'
			);
		}

		if (!preg_match('/^https\:\/\/chat\.googleapis\.com\/v1\/spaces\/.+/m', $webhook))
		{
			throw new \InvalidArgumentException(
				'Invalid Slack webhook ' . $webhook
			);
		}
		self::$webhook = $webhook;
	}

	/**
	 * Post a message to Google Chat
	 * @param array $params
	 */
	public function msg(array $params)
	{
		$params += self::$defultParams;
		$format = [];
		switch ($params['type']) {
			case 'card':
				$format = $this->card_format($params);
				break;
			case 'simple':
				$format = $this->simple_format($params['sections']);
				break;
			
			default:
				throw new Exception ('Invalid type');
				break;
		}

		$this->post_curl(json_encode($format));
	}

	/**
	 * Format a simple message. If it has more than 1 it will only post the first.
	 * @param array $text
	 * @return array $result
	 */
	protected function simple_format(array $text) : array {
		if (count($text) > 1) {
			trigger_error('Simple messages can send only one text section!', E_USER_WARNING);
			
		}

		$result['text'] = $text[0];
		return $result;
	}

	/**
	 * Format a Card message.
	 * @param array $params
	 * @return array $result
	 */
	protected function card_format(array $params) : array {
		$result = array(
			'text' => $params['title'],
			'cards' => array(
				'header' => array(
					'title' => $params['headerTitle'],
					'imageUrl' => $params['headerImageURL'],
					'imageStyle' => 'AVATAR'
				)
			)
		);

		$this->add_card_paragraph_sections($result, $params['sections']);
		$this->add_card_button_sections($result, $params['button']);

		return $result;
	}

	/**
	 * Add all paragraph sections to the card message
	 * @param array @result
	 * @param array @sections
	 */
	protected static function add_card_paragraph_sections(array &$result, array $sections){
		foreach ($sections as $section) {
			$result['cards']['sections'][]['widgets']['textParagraph']['text'] = $section;
		}
	}

	/**
	 * Add a botton at the end of the card message
	 * @param array @result
	 * @param array @buttonParams
	 */
	protected static function add_card_button_sections(array &$result, array $buttonParams){
		$result['cards']['sections'][] = array(
			'widgets' => array(
				'buttons' => array(
					'textButton' => array(
						'text' => $buttonParams['text'],
						'onClick' => array(
							'openLink' => array(
								'url' => $buttonParams['link']
							)
						)
					)
				)
			)
		);
	}

	/**
	 * Post to Google Chat webhook using the "curl" extension
	 * @param string $json
	 */
	protected static function post_curl($json)
	{
		$ch = curl_init(self::$webhook);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

        curl_exec($ch);
        curl_close($ch);
	}
}

