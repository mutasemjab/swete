<?php


function uploadImage($folder, $image)
{
    $extension = strtolower($image->getClientOriginalExtension());

    // generate unique name with timestamp + random string
    $filename = uniqid() . '_' . time() . '.' . $extension;

    $image->move(base_path($folder), $filename);

    return $filename;
}



function uploadFile($file, $folder)
{
    $path = $file->store($folder);
    return $path;
}



/**
 * A user-typed link (e.g. "drive.google.com/xyz") with no scheme renders as a *relative* href —
 * clicking it just reloads/breaks on the current site instead of opening the external site.
 * Prepend https:// when no scheme is present, so it always resolves as absolute.
 */
function normalizeUrl(?string $url): ?string
{
    if ($url === null) {
        return null;
    }

    $url = trim($url);

    if ($url === '') {
        return $url;
    }

    if (! preg_match('#^[a-zA-Z][a-zA-Z0-9+.-]*://#', $url)) {
        $url = 'https://' . $url;
    }

    return $url;
}




