<?php

require_once __DIR__ . '/../dbconnect.php';
require_once __DIR__ . '/pet_image.php';

/**
 * Normalize adoption status for filtering (supports Chinese / English DB values).
 *
 * @return 'available'|'adopted'|'other'
 */
function pets_status_key(string $raw): string
{
    $t = trim($raw);
    if ($t === '') {
        return 'other';
    }
    if (preg_match('/待/u', $t) || preg_match('/available|pending|open/i', $t)) {
        return 'available';
    }
    if (preg_match('/已领|已領|領養完成|adopted|closed/i', $t)) {
        return 'adopted';
    }

    return 'other';
}

/**
 * @return list<array{id:int,name:string,breed:string,age:string,gender:string,genderRaw:string,health:string,personality:string,status:string,statusKey:string,imageUrl:string,listedAt:?string}>
 */
function pets_fetch_all_for_browse(): array
{
    global $conn;

    $sql = 'SELECT pet_id, pet_name, pet_breed, pet_age, pet_gender, pet_health,
                   pet_status, pet_intro, pet_img, create_time
            FROM pets
            ORDER BY pet_id DESC';

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        return [];
    }

    if (!mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        return [];
    }

    mysqli_stmt_bind_result(
        $stmt,
        $pet_id,
        $pet_name,
        $pet_breed,
        $pet_age,
        $pet_gender,
        $pet_health,
        $pet_status,
        $pet_intro,
        $pet_img,
        $create_time
    );

    $out = [];
    while (mysqli_stmt_fetch($stmt)) {
        $genderRaw = (string) $pet_gender;
        $gTrim = trim($genderRaw);
        $male = preg_match('/雄|公|♂/u', $genderRaw)
            || preg_match('/^m(ale)?$/i', $gTrim)
            || ($gTrim === 'M');
        $genderSymbol = $male ? '♂' : '♀';

        $statusRaw = (string) $pet_status;
        $statusKey = pets_status_key($statusRaw);
        $petName = (string) $pet_name;
        $resolvedImageUrl = pet_image_url($petName, (string) $pet_img);

        $out[] = [
            'id' => (int) $pet_id,
            'name' => $petName,
            'breed' => (string) $pet_breed,
            'age' => (string) $pet_age,
            'gender' => $genderSymbol,
            'genderRaw' => $genderRaw,
            'health' => (string) $pet_health,
            'personality' => (string) $pet_intro,
            'status' => $statusRaw,
            'statusKey' => $statusKey,
            'imageUrl' => $resolvedImageUrl,
            'listedAt' => $create_time !== null ? (string) $create_time : null,
        ];
    }

    mysqli_stmt_close($stmt);

    return $out;
}
