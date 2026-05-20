<?php
require_once 'connection.php';

function password()
{
    $words = ['ElEpHaNt', 'BlUeWhAlE', 'rOsE', 'AcCoRdIoN', 'TuRtLe', 'BrOwNBeAr', 'DaIsY', 'XyLoPhOnE', 'ZeBrA', 'PuRpLe', 'KiWi', 'BaNjO', 'MoNkEy', 'OrAnGe', 'SuNsEt', 'GuItAr', 'HiPpOpOtAmUs', 'InDiGo', 'LiLy', 'TrUmPeT', 'LiOn', 'GrEeN', 'PeAcOcK', 'PiAnO', 'LeOpArD', 'BlAcK', 'DaFfOdIl', 'ViOlIn', 'PaNdA', 'ReD', 'OrChId', 'SaXoPhOnE', 'GoRiLlA', 'YeLlOw', 'TuLiP', 'FlUtE', 'KaNgArOo', 'InStRuMeNt', 'RiVeR', 'BaSsOoN', 'RhInOcErOs', 'BrOwN', 'SnAkE', 'BlUe', 'BlUeBeRrY', 'BaNjO', 'SwAn', 'PuRpLe', 'MiMtEr', 'DaIsY', 'PiPeR', 'JaSmInE', 'ViOlEt', 'PiAnO', 'RoBiN', 'PeAcH', 'TrOmBoNe', 'KoAlA', 'GrEeN', 'GrApE', 'HaRp', 'LeOpArD', 'PeAcOcK', 'TuLiP', 'VoLvO', 'ZiThEr', 'GuItAr', 'HiBeRnAtE', 'OcToPuS', 'OrAnGe', 'HoNeYbEe', 'KaLe', 'KaZoO', 'LiLy', 'NoDdEr', 'MaRmOt', 'InDiGo', 'MaNdOlIn', 'MoNkEy', 'MaRoOn', 'PaNsY', 'NaPpEr', 'PaNdA', 'NaVy', 'QuInCe', 'ReD', 'RoSe', 'RyThMiC', 'SaLmOn', 'SnArE', 'TaNgErInE', 'SNoWdRoP', 'ViOlIn', 'ToMpEt', 'WaLrUs', 'TuLlIp', 'XiPhiAs', 'YAk', 'VoCaLiZe', 'YeLlOw', 'WiLlOw', 'WaHoo', 'XyLoPhOnE', 'WiStErIa', 'ZiThEr', 'YaMp', 'ZiNnIa', 'BoSs', 'IvY', 'CeLlO', 'ViNe', 'DaLmAtIaN', 'JoYaBlE', 'FiDdLe', 'FoXgLoVe', 'HaRmOnIcA', 'GuEsS', 'NuTmEg', 'MaNdOlIn', 'OpErA', 'MuSkRaT', 'PeLlE', 'RaSpBeRrY', 'SuSaN', 'TaNgErInE', 'WhIcK', 'ToOtEr', 'YaMp', 'WiStErIa', 'ZaNzIbAr', 'CoRnFlOwEr', 'XiLoPhOnE', 'GyPsY', 'ZuCuChInI', 'HaRmOnIcA', 'IgUaNa', 'InStRuMeNt', 'KoAlA', 'JuJuBe', 'LeMoN', 'LoBtEr', 'MaNdArIn', 'NaStUrTiUm', 'PaNdA', 'QuOkKa', 'RoSe', 'RuMbA', 'StRaWbErRy', 'SuShI', 'TaNgErInE', 'ToMaTo', 'UkUlElE', 'VaNiLlA', 'WeIsSeN', 'XaNtHoNe', 'YaMp', 'ZoNk', 'ViOlIn', 'XyLoPhOnE', 'BlUeWhAlE', 'KaNgArOo'];
    $specialChar = '?=.*([$%^&]).*$/';
    $specChar = '';
    $w1 = $words[array_rand($words)];
    $w2 = $words[array_rand($words)];
    $digits = mt_rand(0, 99);
    for($i=0;$i<2;$i++) {
        $specChar .= $specialChar[mt_rand(1, strlen($specialChar) - 1)];
    }
    return $w1 . $w2 . $digits . $specChar;
}

function hash_pwd(string $pwd)
{
    for ($i = 0; $i < 10; $i++) {
        $pwd = hash("sha256", $pwd);
    }
    return $pwd;
}

function display_users()
{
    global $conn;
    if ($result = $conn->query("SELECT * FROM `users_table`")) {
        ?>

        <table id="user-table" class="table is-striped is-fullwidth is-hoverable">
            <thead>
            <tr>
                <th>ROLE</th>
                <th>SURNAME</th>
                <th>FORENAME</th>
                <th>POSITION</th>
                <th>EMAIL</th>
                <th>PHONE #</th>
                <th>USERNAME</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td class="role"><?= $row['ROLE']; ?></td>
                    <td class="surname"><?= $row['SURNAME']; ?></td>
                    <td class="firstname"><?= $row['FIRSTNAME']; ?></td>
                    <td class="position"><?= $row['POSITION']; ?></td>
                    <td class="email"><?= $row['EMAIL']; ?></td>
                    <td class="contactnum"><?= $row['CONTACTNUM']; ?></td>
                    <td class="username"><?= $row['USERNAME']; ?></td>
                        </a>
                    </td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

    <?php }
}
