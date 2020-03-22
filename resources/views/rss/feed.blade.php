<?=
/* Using an echo tag here so the `<? ... ?>` won't get parsed as short tags */
'<?xml version="1.0" encoding="UTF-8"?>'.PHP_EOL
?>

<feed xmlns="http://www.w3.org/2005/Atom">
    <id>{{ $feed['link'] }}</id>
    <link rel="self" href="{{ $feed['link'] }}"/>
    <title><![CDATA[ {{ $feed['name'] }} ]]></title>
    <updated>{{ $feed['updated'] }}</updated>

    @foreach($feed['data'] as $post)
        <entry>
            <title>{{ $post['title'] }}</title>
            <author>
                <name><![CDATA[ {{ $post['user']['name'] }} ]]></name>
                <email><![CDATA[ {{ $post['user']['email'] }} ]]></email>
            </author>
            <link rel="alternate" href="http://example.org/2003/12/13/atom03"/>
            <id>urn:uuid:1225c695-cfb8-4ebb-aaaa-80da344efa6a</id>
            <published>{{ $post['published_at'] }}</published>
            <summary type="html"><![CDATA[ {!! $post['body'] !!} ]]></summary>
        </entry>
    @endforeach
</feed>
