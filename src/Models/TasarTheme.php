<?php

namespace Tasar\Theme\Models;

use Illuminate\Database\Eloquent\Model;

class TasarTheme extends Model
{
    public $table = 'tasar_themes';
    protected $guarded = ['id'];
    
    public function getThumbnailAttribute()
    {
        $thumbnail = Theme::getThemeInfo($this->dir, 'thumbnail');
        return $thumbnail ? theme_url($thumbnail) : url('assets/images/default_theme_thumbnail.jpg');
    }
}
