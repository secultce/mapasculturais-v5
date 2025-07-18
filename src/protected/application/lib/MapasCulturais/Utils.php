<?php

namespace MapasCulturais;

use Curl\Curl;

class Utils {
    static function removeAccents($string) {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        $chars = array(
            // Decompositions for Latin-1 Supplement
            chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
            chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
            chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
            chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
            chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
            chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
            chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
            chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
            chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
            chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
            chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
            chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
            chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
            chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
            chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
            chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
            chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
            chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
            chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
            chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
            chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
            chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
            chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
            chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
            chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
            chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
            chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
            chr(195) . chr(191) => 'y',
            // Decompositions for Latin Extended-A
            chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
            chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
            chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
            chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
            chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
            chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
            chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
            chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
            chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
            chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
            chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
            chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
            chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
            chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
            chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
            chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
            chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
            chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
            chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
            chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
            chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
            chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
            chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
            chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
            chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
            chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
            chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
            chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
            chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
            chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
            chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
            chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
            chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
            chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
            chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
            chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
            chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
            chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
            chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
            chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
            chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
            chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
            chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
            chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
            chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
            chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
            chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
            chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
            chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
            chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
            chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
            chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
            chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
            chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
            chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
            chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
            chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
            chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
            chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
            chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
            chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
            chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
            chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
            chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's'
        );

        $string = strtr($string, $chars);

        return $string;
    }

    static function slugify($text, string $divider = '-')
    {
        if (empty($text)) {
            return '';
        }

        $text = self::removeAccents($text);

        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        return $text;
    }

    static function isTheSameName($name1, $name2) {
        if(self::slugify($name1) == self::slugify($name2)) {
            return true;
        }
        $len = max(strlen($name1), strlen($name2));
        if ($len < 10) {
            $cutoff = 95;
        } else if ($len < 15) {
            $cutoff = 90;
        } else if ($len < 20) {
            $cutoff = 85;
        } else if ($len >= 20) {
            $cutoff = 80;
        }

        similar_text(self::slugify($name1), self::slugify($name2), $similarity);
        
        if($similarity >= $cutoff) {
            return true;
        }

        return false;
    }

    static function formatCnpjCpf($value) {
      $CPF_LENGTH = 11;
      $cnpj_cpf = preg_replace("/\D/", '', $value);
      
      if (strlen($cnpj_cpf) === $CPF_LENGTH) {
        return preg_replace("/(\d{3})(\d{3})(\d{3})(\d{2})/", "\$1.\$2.\$3-\$4", $cnpj_cpf);
      } 
      
      return preg_replace("/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/", "\$1.\$2.\$3/\$4-\$5", $cnpj_cpf);
    }

    static function checkUserHasSeal($sealId)
    {
        $user = App::i()->getUser();
        //Verificação de usuário
        $sealRelations = $user->profile ? $user->profile->sealRelations : [];

        $hasSeal = array_filter($sealRelations, function ($sealRelation) use ($sealId) {
            return $sealRelation->seal->id === (int)$sealId;
        });

        return $hasSeal;
    }

    static function getTermsByOpportunity($text, $opportunity)
    {
        $terminology = [
            'Avaliador' => 'Fiscal',
            'avaliador' => 'fiscal',
            'Avaliadores' => 'Fiscais',
            'avaliadores' => 'fiscais',
            'Avaliação' => 'Monitoramento',
            'avaliação' => 'monitoramento',
            'a avaliação' => 'o monitoramento',
            'da avaliação' => 'do monitoramento',
            'avaliação encontrada' => 'monitoramento encontrado',
            'Nenhuma avaliação enviada' => 'Nenhum monitoramento enviado',
            'Avaliações' => 'Monitoramentos',
            'avaliações' => 'monitoramentos',
            'as avaliações' => 'os monitoramentos',
            'das avaliações' => 'dos monitoramentos',
            'Suas avaliações' => 'Seus monitoramentos',
            'todas as <b>avaliações</b>' => 'todos os <b>monitoramentos</b>',
            'Avaliado' => 'Monitorado',
            'avaliado' => 'monitorado',
            'Avaliada' => 'Monitorada',
            'avaliada' => 'monitorada',
            'Enviar inscrição' => 'Enviar prestação de contas'
        ];


        if (!method_exists($opportunity, 'getMetadata')) {
            return $text;
        }

        if ($opportunity->getMetadata('use_multiple_diligence') === 'Sim') {
            $text = strtr($text, $terminology);
        }

        return $text;
    }

    static function saveFileByUrl($url, $owner, $group)
    {
        try {
            $exp = explode(":", $url);

            $_file = $exp[0] . ":" . $exp[1];
            $description = isset($exp[2]) ? $exp[2] : null;

            $basename = basename($_file);
            $file_data = str_replace($basename, urlencode($basename), $_file);

            $curl = new Curl;
            $curl->get($file_data);
            $curl->close();
            $response = $curl->response;

            $tmp = tempnam("/tmp", "");
            $handle = fopen($tmp, "wb");

            if (mb_strpos($response, 'html')) {
                fclose($handle);
                unlink($tmp);
                return false;
            }

            if (!self::urlFileExists($_file)) {
                fclose($handle);
                unlink($tmp);
                return false;
            }

            fwrite($handle, $response);
            fclose($handle);

            $class_name = $owner->fileClassName;

            $savedFile = App::i()->repo($class_name)->findOneBy([
                'owner' => $owner,
                'group' => $group,
            ]);
            if ($savedFile) $savedFile->delete(true);

            $file = new $class_name([
                "name" => $basename,
                "type" => mime_content_type($tmp),
                "tmp_name" => $tmp,
                "error" => 0,
                "size" => filesize($tmp)
            ]);

            $file->group = $group;
            $file->owner = $owner;
            $file->description = $description;
            $file->save(true);
            return true;
        } catch (\Throwable $th) {
            return false;
        }
    }

    static function urlFileExists($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($code == 200);
    }
}
