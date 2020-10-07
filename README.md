# GleChatS
--- 
GleChatS (short from **G**~~oog~~**LE** **CHAT** ~~Webhook~~**S**), is a small library for posting messages to Google Chat, using their webhooks.

# Message Types
---
There are two message types: simple and card.
For the simple one you need to provide only one section with the text you will send.

For the card type there are several params you can add to your message:
    * title -> text
    * headerTitle -> text
    * headerImageUrl -> Image URL
    * sections -> provide an array of paragraphs you want to include in the card message.
    * button -> array of KVP that contains text and link


# Basic use
---
You create a new object by providing the Google Chat Webhook

	$glechats = new glechats('https://chat.googleapis.com/v1/spaces/AAAA-EgR1hE/messages?....');

Once you have an object created, you can start posting messages:

    $glechats->msg([
        'type'=>'simple',
        'sections' => ['Hello World!']
    ]);

# Example
---
Simple message

    $glechats = new glechats('https://chat.googleapis.com/v1/spaces/AAAA-EgR1hE/messages?....');
    $params = [
        'type' => 'simple',
        'sections' => ['Hello World!']
    ];
    $glechats->msg($params);

    
Card Message

    $glechats = new glechats('https://chat.googleapis.com/v1/spaces/AAAA-EgR1hE/messages?....');
    $params = [
        'type' => 'card',
        'title' => 'Hello!',
        'headerTitle' => 'World!',
        'headerImageUrl' => 'https://example/image',
        'sections' => [
            'Several',
            'Line',
            'Message With a button at the end'
        ],
        'button' => [
            'text' => 'Button Text',
            'link' => 'https://example/button/link'
        ]
    ];