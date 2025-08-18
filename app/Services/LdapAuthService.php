<?php
namespace App\Services;

class LdapAuthService
{
    /**
     * Login ke LDAP dengan email (UPN) + password user.
     * Jika bind langsung gagal dan ada service account, fallback:
     *  - bind pakai service account, cari DN user dari mail,
     *  - lalu re-bind sebagai DN user + password user.
     *
     * return: ['cn' => ?string, 'mail' => string] | null
     */
    public function attempt(string $email, string $password): ?array
    {
        $host    = env('LDAP_HOST', '10.16.8.8');
        $port    = (int) env('LDAP_PORT', 389);
        $baseDn  = env('LDAP_BASE_DN', 'DC=sdn,DC=id');
        $timeout = (int) env('LDAP_TIMEOUT', 5);

        $conn = @ldap_connect($host, $port);
        if (!$conn) return null;

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        ldap_set_option($conn, LDAP_OPT_NETWORK_TIMEOUT, $timeout);

        // 1) Bind langsung pakai UPN (email)
        if (@ldap_bind($conn, $email, $password)) {
            return $this->fetchUserByMail($conn, $baseDn, $email) ?? ['mail' => $email, 'cn' => null];
        }

        // 2) Fallback: bind service account (jika diset di .env)
        $bindDn   = env('LDAP_BIND_DN');       // contoh: CN=ldap-bind,OU=Service,DC=sdn,DC=id
        $bindPass = env('LDAP_BIND_PASSWORD');

        if ($bindDn && $bindPass && @ldap_bind($conn, $bindDn, $bindPass)) {
            $dn = $this->findDnByMail($conn, $baseDn, $email);
            if (!$dn) return null;

            if (!@ldap_bind($conn, $dn, $password)) {
                return null;
            }
            $entry = $this->fetchUserByMail($conn, $baseDn, $email);
            return $entry ?? ['mail' => $email, 'cn' => null];
        }

        return null;
    }

    private function findDnByMail($conn, string $baseDn, string $email): ?string
    {
        $filter = '(mail=' . $this->escapeFilter($email) . ')';
        $sr = @ldap_search($conn, $baseDn, $filter, ['dn']);
        if (!$sr) return null;
        $entries = @ldap_get_entries($conn, $sr);
        if ($entries === false || ($entries['count'] ?? 0) < 1) return null;
        return $entries[0]['dn'] ?? null;
    }

    private function fetchUserByMail($conn, string $baseDn, string $email): ?array
    {
        $filter = '(mail=' . $this->escapeFilter($email) . ')';
        $sr = @ldap_search($conn, $baseDn, $filter, ['cn','mail']);
        if (!$sr) return null;
        $entries = @ldap_get_entries($conn, $sr);
        if ($entries === false || ($entries['count'] ?? 0) < 1) return null;

        return [
            'cn'   => $entries[0]['cn'][0]   ?? null,
            'mail' => $entries[0]['mail'][0] ?? $email,
        ];
    }

    private function escapeFilter(string $value): string
    {
        return addcslashes($value, "\\*()\x00");
    }
}
