<?php

use Glpi\Plugin\Hooks;
use GLPIPlugin\TARSignature\TARSignature;

define('TARSIGNATURE_VERSION', '1.0.0');

/**
 * Init the hooks of the plugins - Needed
 *
 * @return void
 */
function plugin_init_tarsignature()
{
   global $PLUGIN_HOOKS;

   //required!
   $PLUGIN_HOOKS['csrf_compliant']['tarsignature'] = true;
   $PLUGIN_HOOKS[Hooks::ADD_JAVASCRIPT]['tarsignature'] = 'tarsignature.js';

   Plugin::registerClass(TARSignature::class);
}

/**
 * Get the name and the version of the plugin - Needed
 *
 * @return array
 */
function plugin_version_tarsignature()
{
   return [
      'name' => 'TAR Signature',
      'version' => TARSIGNATURE_VERSION,
      'author' => 'TAR Signature team',
      'license' => 'GLPv3',
      'homepage' => 'http://',
      'requirements' => [
         'glpi' => [
            'min' => '9.1'
         ]
      ]
   ];
}

/**
 * Optional : check prerequisites before install : may print errors or add to message after redirect
 *
 * @return boolean
 */
function plugin_tarsignature_check_prerequisites()
{
   global $DB;

   // Obter o token de app_tokens
   $result = $DB->query("SELECT app_token FROM glpi_apiclients");
   $result = $result->fetch_assoc();
   $app_token = $result['app_token'];

   // Obter o token de api
   $result = $DB->query("SELECT api_token FROM glpi_users");
   $result = $result->fetch_assoc();
   $api_token = $result['api_token'];

   // Se não foi encontrar o app token
   if (empty($app_token)) {
      echo 'Gere um App Token para ativar este plugin';
      return false;
   }

   // Se não foi possível encontrar a api token
   if (empty($api_token)) {
      echo 'Gere um Token de API para ativar este plugin';
      return false;
   }
   
   return true;
}

/**
 * Check configuration process for plugin : need to return true if succeeded
 * Can display a message only if failure and $verbose is true
 *
 * @param boolean $verbose Enable verbosity. Default to false
 *
 * @return boolean
 */
function plugin_tarsignature_check_config($verbose = false)
{
   if (true) { // Your configuration check
      return true;
   }

   if ($verbose) {
      echo "Installed, but not configured";
   }
   return false;
}

/**
 * Optional: defines plugin options.
 *
 * @return array
 */
function plugin_tarsignature_options()
{
   return [
      Plugin::OPTION_AUTOINSTALL_DISABLED => true,
   ];
}