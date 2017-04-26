<?php

namespace PatroNet\Core\I18n;


// FIXME/TODO
/**
 * Interface for multilingual objects
 */
interface Translatable 
{
    
    const FORMAT_ISO6391 = "format_iso6391";
    const FORMAT_ISO6392 = "format_iso6392";
    const FORMAT_LOCALE = "format_locale";
    const FORMAT_LOCALE_UNDERSCORE = "format_locale_underscore";
    
    // FIXME
    /**
     * Sets the language of the object
     *
     * @param string $lang
     */
    public function setLanguage($lang);
    
    // FIXME
    /**
     * Gets the actual language of the object
     *
     * @return string
     */
    public function getActualLanguage();
    
    // FIXME
    /**
     * Gets the current language of the object
     *
     * @param string $flag
     * @return string
     */
    public function getLanguage($flag);
    
}
