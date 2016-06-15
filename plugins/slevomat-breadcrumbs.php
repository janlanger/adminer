<?php

class SlevomatBreadcrumbs
{

    private static $superUsers = [
        'vlasta' => 'ubervlasta',
        'jarda' => 'superjarda',
        'ondra' => 'superondra',
        'honzal' => 'superhonzal',
    ];

    private static $servers = [
        'Slevomat' => [
            'kacka.stable.cz' => 'kacka.stable.cz (master)',
            'kacka.stable.cz:3307' => 'kacka.stable.cz:3307 (master)',
            'rampa.stable.cz' => 'rampa.stable.cz (master2)',
            'rampa.stable.cz:3307' => 'rampa.stable.cz:3307 (master2)',
            'bubik.stable.cz' => 'bubik.stable.cz (slave-delay)',
            'bubik.stable.cz:3307' => 'bubik.stable.cz:3307 (slave-delay)',
            'dulik.stable.cz' => 'dulik.stable.cz (dev)',
            'dulik.stable.cz:3307' => 'dulik.stable.cz:3307 (dev)',
        ],
        'Sofistic' => [
            'lorem.stable.cz' => 'lorem.stable.cz (sofistic master)',
            'ipsum.stable.cz' => 'ipsum.stable.cz (sofistic slave)',
            'dolor.stable.cz' => 'dolor.stable.cz (sofistic dev)',
        ],
        'Localhost' => [
            'localhost' => 'localhost',
        ],
    ];

    /**
     * @param mixed[]|bool $breadcrumb
     * @param string $title
     * @return bool
     */
    public function breadcrumbs($breadcrumb, string $title): bool
    {
        global $drivers;

        $link = substr(preg_replace('~\b(username|db|ns)=[^&]*&~', '', ME), 0, -1);
        echo '<p id="breadcrumb"><a href="' . h($link ? $link : ".") . '">' . $drivers[DRIVER] . '</a> &raquo; ';

        $link = substr(preg_replace('~\b(db|ns)=[^&]*&~', '', ME), 0, -1);
        $imageLink = dirname(preg_replace('~\\?.*~', '', ME)) . "/static/servers/" . explode(':', SERVER)[0] . ".png";
        $serverImage = "<a href='" . ($link ? h($link) : ".") . "' accesskey='1' title='Alt+Shift+1'><img src='$imageLink' width='16' height='16'></a>";

        $serverItems = isset(self::$servers[SERVER]) || isset(self::$servers['Slevomat'][SERVER]) || isset(self::$servers['Sofistic'][SERVER]) || isset(self::$servers['Localhost'][SERVER])
            ? self::$servers
            : array_merge(self::$servers, [SERVER => SERVER]);

        $baseUrl = $this->addPostQuery($_SERVER['REQUEST_URI']);
        $servers = select_input(
            " data-url='$baseUrl' onchange='javascript: window.location = this.getAttribute(\"data-url\").toString().replace(/(server|elastic|mongo)=[^&]+/i, \"server=\" + this.value)'",
            $serverItems,
            SERVER
        );
        $user = !empty($_GET['username']) ? $_GET['username'] : null;

        echo "$serverImage $servers";
        if ($user) {
            echo " &raquo; <b>$user</b>";
            if (in_array($user, self::$superUsers)) {
                $toUser = array_search($user, self::$superUsers);
                echo ' (<a href="' . $this->addPostQuery(str_replace($user, $toUser, $_SERVER['REQUEST_URI'])) . '"">' . h($toUser) . '</a>)';
            } elseif (array_key_exists($user, self::$superUsers)) {
                $toUser = self::$superUsers[$user];
                echo ' (<a href="' . $this->addPostQuery(str_replace($user, $toUser, $_SERVER['REQUEST_URI'])) . '">' . h($toUser) . '</a>)';
            }
        }

        if ($breadcrumb !== false) {
            echo " &raquo; ";
            if ($_GET["ns"] != "" || DB != "") {
                echo '<a href="' . h($link . "&db=" . urlencode(DB) . (support("scheme") ? "&ns=" : "")) . '"><b>' . h(DB) . '</b></a>';
                if (DB === 'slevomat') {
                    echo ' (<a href="' . $this->addPostQuery(str_replace('slevomat', 'zlavomat', $_SERVER['REQUEST_URI'])) . '">' . h('zlavomat') . '</a>)';
                } elseif (DB === 'zlavomat') {
                    echo ' (<a href="' . $this->addPostQuery(str_replace('zlavomat', 'slevomat', $_SERVER['REQUEST_URI'])) . '">' . h('slevomat') . '</a>)';
                }
                if (is_array($breadcrumb)) {
                    echo ' &raquo; ';
                }
            }
            if (is_array($breadcrumb)) {
                if ($_GET["ns"] != "") {
                    echo '<a href="' . h(substr(ME, 0, -1)) . '">' . h($_GET["ns"]) . '</a> &raquo; ';
                }
                foreach ($breadcrumb as $key => $val) {
                    $desc = (is_array($val) ? $val[1] : h($val));
                    if ($desc != "") {
                        echo "<a href='" . h(ME . "$key=") . urlencode(is_array($val) ? $val[0] : $val) . "'>$desc</a> &raquo; ";
                    }
                }
            }
            if (is_array($breadcrumb)) {
                echo "$title\n";
            }
        }

        return false;
    }

    private function addPostQuery(string $url): string
    {
        if (!empty($_POST['query']) && strlen($_POST['query']) < 1000) {
            return preg_replace('/sql=[^&]*/', 'sql=' . urldecode($_POST['query']), $url);
        } else {
            return $url;
        }
    }

}
