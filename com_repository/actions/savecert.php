<?php
/**
 * Save the repository certificate.
 *
 * @package Components\repository
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 * @author Hunter Perrin <hperrin@gmail.com>
 * @copyright SciActive.com
 * @link http://sciactive.com/
 */
/* @var $pines pines */
defined('P_RUN') or die('Direct access prohibited');

if ( !gatekeeper('com_repository/gencert') )
	punt_user(null, pines_url('com_repository', 'gencert'));

$dn = array(
	'countryName' => $_REQUEST['countryName'],
	'stateOrProvinceName' => $_REQUEST['stateOrProvinceName'],
	'localityName' => $_REQUEST['localityName'],
	'organizationName' => $_REQUEST['organizationName'],
	'organizationalUnitName' => $_REQUEST['organizationalUnitName'],
	'commonName' => $_REQUEST['commonName'],
	'emailAddress' => $_REQUEST['emailAddress']
);

$configargs = array(
	'digest_alg' => $_REQUEST['digest_alg'],
	'private_key_bits' => $_REQUEST['private_key_bits']
);

// Generate a new private (and public) key pair.
$privkey = openssl_pkey_new();

// Generate a certificate signing request.
$csr = openssl_csr_new($dn, $privkey, $configargs);

// Creates a self-signed cert.
$sscert = openssl_csr_sign($csr, null, $privkey, (int) $_REQUEST['days']);

// Export private key, CSR, and cert.
openssl_csr_export($csr, $csrout);
openssl_x509_export($sscert, $certout);
if (empty($_REQUEST['password'])) {
	openssl_pkey_export($privkey, $pkeyout);
} else {
	openssl_pkey_export($privkey, $pkeyout, $_REQUEST['password']);
}

// Show any errors that occurred here
while (($e = openssl_error_string()) !== false) {
    pines_log("OpenSSL Error: $e");
}

if (empty($csrout) || empty($certout) || empty($pkeyout)) {
	pines_error('Error occurred generating certificate. Check the log for more details.');
	pines_action('com_repository', 'gencert');
	return;
}

// Make sure private dir exists.
$dir = "{$pines->config->com_repository->repository_path}private/";
if (!file_exists($dir) && !mkdir($dir, 0700)) {
	pines_error('Could not create private path in repository.');
	pines_action('com_repository', 'gencert');
	return;
}
if (!chmod($dir, 0700)) {
	pines_error('Could not set mode on private path.');
	pines_action('com_repository', 'gencert');
	return;
}

$htaccess = <<<EOF
# Block this folder from prying eyes.
order allow,deny
deny from all
EOF;

// Write htaccess file.
if (!file_put_contents("{$dir}.htaccess", $htaccess)) {
	pines_error('Could not write htaccess file.');
	pines_action('com_repository', 'gencert');
	return;
}

// Write private key.
if (!file_put_contents("{$dir}cert.key", $pkeyout) || !chmod("{$dir}cert.key", 0700)) {
	pines_error('Could not write private key.');
	pines_action('com_repository', 'gencert');
	return;
}
// Write cert.
if (!file_put_contents("{$dir}cert.pem", $certout) || !chmod("{$dir}cert.pem", 0700)) {
	pines_error('Could not write cert.');
	pines_action('com_repository', 'gencert');
	return;
}
// Write CSR.
if (!file_put_contents("{$dir}cert.csr", $csrout) || !chmod("{$dir}cert.csr", 0700)) {
	pines_error('Could not write CSR.');
	pines_action('com_repository', 'gencert');
	return;
}

pines_notice('Generated certificate successfully.');

pines_redirect(pines_url('com_repository', 'viewcert'));