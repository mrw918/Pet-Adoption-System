<?php

/**
 * Convert pet name to plain lowercase basename (e.g. "A Fu" => "afu").
 */
function pet_image_basename_from_name(string $name): string
{
    $lower = strtolower(trim($name));
    if ($lower === '') {
        return '';
    }

    $slug = preg_replace('/[^a-z0-9]+/', '', $lower);
    if (!is_string($slug) || $slug === '') {
        return '';
    }

    return $slug;
}

/**
 * Resolve unified pet image URL used across pages.
 */
function pet_image_url(string $petName, string $dbPetImg = ''): string
{
    $assetPrefix = '/assets/img';
    $defaultImage = $assetPrefix . '/default-pet.svg';
    $assetDiskDir = realpath(__DIR__ . '/../assets/img') ?: (__DIR__ . '/../assets/img');
    $allowedExt = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    $findLocalByStem = static function (string $stem) use ($assetDiskDir, $assetPrefix, $allowedExt): ?string {
        $stem = trim($stem);
        if ($stem === '') {
            return null;
        }

        foreach ($allowedExt as $ext) {
            $candidate = $assetDiskDir . '/' . $stem . '.' . $ext;
            if (is_file($candidate)) {
                return $assetPrefix . '/' . rawurlencode($stem) . '.' . $ext;
            }
        }
        return null;
    };

    // 1) Exact name match in assets/img, e.g. Lele -> lele.jpg or 阿福 -> 阿福.jpg
    $byExact = $findLocalByStem($petName);
    if ($byExact !== null) {
        return $byExact;
    }

    // 2) Slug fallback for latin names, e.g. "Lele" -> "lele.jpg"
    $base = pet_image_basename_from_name($petName);
    if ($base !== '') {
        $bySlug = $findLocalByStem($base);
        if ($bySlug !== null) {
            return $bySlug;
        }
    }

    $fallback = trim($dbPetImg);
    if ($fallback !== '') {
        if (str_starts_with($fallback, $assetPrefix . '/')) {
            return $fallback;
        }
    }

    return $defaultImage;
}
