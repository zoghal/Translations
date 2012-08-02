<?php
/**
 * The default language should always be defined
 *
 * If it's not defined, it is assumed to be 'en'
 */
if (!Configure::write('Config.language')) {
	Configure::write('Config.language', 'en');
}
