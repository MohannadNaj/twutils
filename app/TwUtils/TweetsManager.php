<?php

namespace App\TwUtils;

use App\Tweep;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class TweetsManager
{
    public static function mapResponseToTweet(array $tweet, Tweep $tweep, $taskId): array
    {
        return [
            'id_str'                  => $tweet['id_str'],
            'extended_entities'       => json_encode($tweet['extended_entities'] ?? []),
            'text'                    => $tweet['full_text'],
            'lang'                    => $tweet['lang'],
            'retweet_count'           => $tweet['retweet_count'] ?? null,
            'favorite_count'          => isset($tweet['retweeted_status']) ? $tweet['retweeted_status']['favorite_count'] : $tweet['favorite_count'],
            'tweet_created_at'        => Carbon::createFromTimeString($tweet['created_at']),
            'tweep_id'                => $tweep->id,
            'in_reply_to_screen_name' => $tweet['in_reply_to_screen_name'] ?? null,
            'mentions'                => Str::limit(collect(Arr::get($tweet, 'entities.user_mentions', []))->implode('screen_name', ','), 190),
            'hashtags'                => Str::limit(collect(Arr::get($tweet, 'entities.hashtags', []))->implode('text', ','), 190),
            'is_quote_status'         => $tweet['is_quote_status'] ?? false,
            'quoted_status'           => isset($tweet['quoted_status']) ? json_encode($tweet['quoted_status']) : null,
            'quoted_status_permalink' => isset($tweet['quoted_status_permalink']) ? json_encode($tweet['quoted_status_permalink']) : null,
            'retweeted_status'        => isset($tweet['retweeted_status']) ? json_encode($tweet['retweeted_status']) : null,
        ];
    }
}
