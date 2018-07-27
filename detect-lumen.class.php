<?php
namespace Dropcode\Color;


class detectLumen {

    public $luminance = 0;
    public $color = null;
    public $info = null;

    /**
     * Class Contstructor
     * @param $elm string Color or image-path
     * @param $samples integer How many samples to use
     */
    function __construct( $elm = null, $samples = 10 )
    {
        if( $this->is_color($elm) ) {
            $this->luminance = $this->color->lightness;
        } else {
           $this->luminance = $this->avg_luminance($elm, $samples );
        }
    }

    public function lumen()
    {
        return $this->luminance;
    }

    public function is( $threshold = 170) 
    {
        return $this->luminance > $threshold ? 'light' : 'dark';
    }

    private function is_color($elm)
    {
        if( strpos($elm, '#') !== false ) {
            $rgb = $this->HexToRGB($elm);
            $this->color = $this->RGBToHSL($rgb);
            return true;
        } elseif( strpos($elm, 'rgb') !== false ) {
            $rgb = $this->parseRGB($elm);
            $this->color = $this->RGBToHSL($rgb);
            return true;   
        } else {
            return false;
        }
    }

    private function parseRGB($str) {
        $str = str_replace("rgba(", "", $str);
        $str = str_replace(")", "", $str);
        $str = str_replace("rgb(", "", $str);
        list($r,$g,$b) = explode(",", $str);
        return $b + ($g << 0x8) + ($r << 0x10);
    }

    private function avg_luminance($filename, $num_samples=10) 
    {
        $file_parts = pathinfo($filename);

        switch($file_parts['extension'])
        {
            case "jpg": case "jpeg": default:
                $img = imagecreatefromjpeg($filename);
            break;
            case "png":
                $img = imagecreatefrompng($filename);
            break;
            case "gif":
                $img = imagecreatefromgif($filename);
            break;
        }
        
        $width = imagesx($img);
        $height = imagesy($img);

        $x_step = intval($width/$num_samples);
        $y_step = intval($height/$num_samples);

        $total_lum = 0;
        $sample_no = 1;

        for ($x=0; $x<$width; $x+=$x_step) {
            for ($y=0; $y<$height; $y+=$y_step) {
                $rgb = imagecolorat($img, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                $lum = ($r+$r+$b+$g+$g+$g)/6;
                $total_lum += $lum;
                // debugging code
                //echo "$sample_no - XY: $x,$y = $r, $g, $b = $lum<br />";
                $sample_no++;
            }
        }
        return $total_lum/$sample_no;
    }

    private function HexToRGB($hex)
    {
        if($hex[0] == '#')
        $hex = substr($hex, 1);

        if (strlen($hex) == 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = hexdec($hex[0] . $hex[1]);
        $g = hexdec($hex[2] . $hex[3]);
        $b = hexdec($hex[4] . $hex[5]);
        return $b + ($g << 0x8) + ($r << 0x10);
    }

    private function RGBtoHEX($RGB) {

    }

    private function RGBToHSL($RGB) {
        $r = 0xFF & ($RGB >> 0x10);
        $g = 0xFF & ($RGB >> 0x8);
        $b = 0xFF & $RGB;

        $r = ((float)$r) / 255.0;
        $g = ((float)$g) / 255.0;
        $b = ((float)$b) / 255.0;

        $maxC = max($r, $g, $b);
        $minC = min($r, $g, $b);

        $l = ($maxC + $minC) / 2.0;

        if($maxC == $minC) {
          $s = 0;
          $h = 0;
        }
        else {
          if($l < .5) {
            $s = ($maxC - $minC) / ($maxC + $minC);
          } else {
            $s = ($maxC - $minC) / (2.0 - $maxC - $minC);
          }
          if($r == $maxC)
            $h = ($g - $b) / ($maxC - $minC);
          if($g == $maxC)
            $h = 2.0 + ($b - $r) / ($maxC - $minC);
          if($b == $maxC)
            $h = 4.0 + ($r - $g) / ($maxC - $minC);

          $h = $h / 6.0; 
        }

        $h = (int)round(255.0 * $h);
        $s = (int)round(255.0 * $s);
        $l = (int)round(255.0 * $l);

        return (object) Array('hue' => $h, 'saturation' => $s, 'lightness' => $l);
    }

}