<?php
namespace x51\functions;

class funcHashtag{
	const MASK = '/#([^\b#@\s]+)/';
	const MASK_TAG = '/#{tag}[\b#@\s]*/';
	
	public static function getHashtags($message) { // вернуть список хештегов из сообщения
		if ($message && is_string($message)) {
			$arMatches=array();
			if (preg_match_all(static::MASK, $message, $arMatches, PREG_PATTERN_ORDER)) {
				return array_unique($arMatches[1]);
			}
		}
		return array();
	} // end getHashtags
	
	public static function checkTag($text, $tag) { // проверяет наличие одного тега в тексте
		if ($text && $tag && is_string($text) && is_string($tag)) {
			return preg_match(str_replace('{tag}', $tag, static::MASK_TAG), $text) > 0 ? true : false;
		} else {
			return false;
		}
	} // end checkTag
	
	public static function checkTags($text, array $arTag) { // проверяет наличие тегов в тексте
		if ($text && $arTag) {
			foreach ($arTag as $tag) {
				if (! static::checkTag($text, $tag)) {
					return false;
				}
			}
			return true;
		} else {
			return false;
		}
	} // end checkTag
	
	public static function replaceHashtagPattern($message, $pattern='<a target="_blank" href="https://www.facebook.com/hashtag/#hashtag>##hashtag</a>') {
		if ($message &&  $pattern && is_string($message)) {
			return preg_replace_callback(static::MASK, function (array $matches) use ($pattern) {
				return str_replace('#hashtag', strip_tags(trim($matches[1])), $pattern);
			}, $message);
		}
		return $message;
	} // hashtag2link
	
	public static function hashtag2link($message, $hashtagUrlPattern='https://www.facebook.com/hashtag/#hashtag', $class='hashtag', $target=false) {
		return static::replaceHashtagPattern(
			$message,
			'<a'.($target ? ' target="'.$target.'"' : '').' href="'.$hashtagUrlPattern.'"'.($class ? ' class="'.$class.'"' : '').'>##hashtag</a>'
		);
	} // hashtag2link
} // end class