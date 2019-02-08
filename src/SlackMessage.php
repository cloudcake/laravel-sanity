<?php

namespace Sanity;

use Zttp\Zttp;

class SlackMessage
{
    /**
     * Webhook URL.
     *
     * @var string
     */
    private $webhook;

    /**
     * Message payload.
     *
     * @var array
     */
    private $payload = [
      'attachment' => [],
    ];

    /**
     * New instance of SlackMessage.
     *
     * @param string $webhook Webhook URL to post to.
     *
     * @return void
     */
    public function __construct($webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Set username.
     *
     * @return self
     */
    public function username($username)
    {
        $this->payload['username'] = $username;

        return $this;
    }

    /**
     * Set attachment title.
     *
     * @return self
     */
    public function title($title)
    {
        $this->payload['attachments'][0]['title'] = $title;

        return $this;
    }

    /**
     * Set attachment text.
     *
     * @return self
     */
    public function text($text)
    {
        $this->payload['attachments'][0]['text'] = $text;

        return $this;
    }

    /**
     * Set attachment pretext.
     *
     * @return self
     */
    public function pretext($pretext)
    {
        $this->payload['attachments'][0]['pretext'] = $pretext;

        return $this;
    }

    /**
     * Set attachment footer.
     *
     * @return self
     */
    public function footer($footer)
    {
        $this->payload['attachments'][0]['footer'] = $footer;

        return $this;
    }

    /**
     * Set attachment colour to green.
     *
     * @return self
     */
    public function success()
    {
        $this->payload['attachments'][0]['color'] = '#99cc00';

        return $this;
    }

    /**
     * Set attachment colour to red.
     *
     * @return self
     */
    public function danger()
    {
        $this->payload['attachments'][0]['color'] = '#c53232';

        return $this;
    }

    /**
     * Add attachment field.
     *
     * @return self
     */
    public function field($title, $value, $short = true)
    {
        $this->payload['attachments'][0]['fields'][] = [
          'title' => $title,
          'value' => $value,
          'short' => $short,
        ];

        return $this;
    }

    /**
     * Add attachment action.
     *
     * @return self
     */
    public function action($type, $text, $url, $style = '')
    {
        $this->payload['attachments'][0]['actions'][] = [
          'type' => $type,
          'text' => $text,
          'url'  => $url,
          'style'=> $style,
        ];

        return $this;
    }

    /**
     * Send the message.
     *
     * @return void
     */
    public function send()
    {
        $this->payload['attachments'][0]['ts'] = time();

        Zttp::asJson()->post($this->webhook, $this->payload);
    }
}
