<?php

namespace v3project\hiccup;

use yii\helpers\Html;

class Hiccup {

    public static function hiccup($hiccup) {
        if (!is_array($hiccup)) return strval($hiccup);

        if (empty($hiccup)) return '';

        $first_key = key($hiccup);
        if (is_string($first_key)) return $hiccup;

        $ret = '';

        $tags = array_shift($hiccup);
        if (($tags === null) OR ($tags === false)) $tags = [$tags];
        else {
            $tags = explode('>', $tags);
            $tags = array_reverse($tags);
        }
        foreach ($tags as $tag) {
            $matches = [];
            if (preg_match('/^([-_\w]+)(\#[-_\w]+)?([-_\.\w]+)?$/iu', $tag, $matches)) {
                $tag_name = $matches[1];
                $hoptions = [];
                if (!empty($matches[2])) $hoptions['id'] = ltrim($matches[2], '#');
                if (!empty($matches[3])) {
                    $hoptions['class'] = preg_split('/[\.\s]+/', $matches[3], -1, PREG_SPLIT_NO_EMPTY);
                }
            }
            else {
                $tag_name = $tag;
                $hoptions = [];
            }

            if ($ret === '') {
                $content = [];
                foreach ($hiccup as $el) {
                    if (!isset($el)) continue;
                    $el = Hiccup::hiccup($el);
                    if (is_array($el)) {
                        $hoptions = array_merge_recursive($hoptions, $el);
                        if (isset($hoptions['class']) and !is_array($hoptions['class'])) $hoptions['class'] = preg_split('/\s+/', $hoptions['class'], -1, PREG_SPLIT_NO_EMPTY);
                    }
                    else {
                        $content[] = strval($el);
                    }
                }
                $ret = implode('', $content);
            }

            $ret = Html::tag($tag_name, $ret, $hoptions);
        }
        return strval($ret);

    }
}