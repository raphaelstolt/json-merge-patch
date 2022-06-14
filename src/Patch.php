<?php

namespace Rs\Json\Merge;

class Patch
{
    public const MEDIA_TYPE = "application/merge-patch+json";

    /**
     * Apply patch on target document.
     *
     * @param  mixed $targetDocument
     * @param  mixed $patchDocument
     * @return mixed
     */
    public function apply($targetDocument, $patchDocument)
    {
        if ($targetDocument === null || !is_object($targetDocument) || is_array($targetDocument)) {
            $targetDocument = new \stdClass();
        }

        if ($patchDocument === null || !is_object($patchDocument) || is_array($patchDocument)) {
            return $patchDocument;
        }

        foreach ($patchDocument as $key => $value) {
            if ($value === null) {
                unset($targetDocument->$key);
            } else {
                if (!isset($targetDocument->$key)) {
                    $targetDocument->$key = null;
                }
                $targetDocument->$key = $this->apply(
                    $targetDocument->$key,
                    $value
                );
            }
        }

        return $targetDocument;
    }

    /**
     * Generate patch for given source document via target document.
     *
     * @param mixed $sourceDocument
     * @param mixed $targetDocument
     *
     * @return mixed
     */
    public function generate($sourceDocument, $targetDocument)
    {
        if ($sourceDocument === null || $targetDocument === null) {
            return $targetDocument;
        }

        if ($sourceDocument == new \stdClass()) {
            return null;
        }

        if (is_array($sourceDocument)) {
            if ($sourceDocument !== $targetDocument) {
                return $targetDocument;
            }

            return null;
        }

        $patchDocument = new \stdClass();
        $sourceDocumentVars = get_object_vars($sourceDocument);
        $targetDocumentVars = get_object_vars($targetDocument);

        foreach ($targetDocumentVars as $var => $value) {
            if (!in_array($var, array_keys($sourceDocumentVars))
                || !in_array($value, array_values($sourceDocumentVars))
            ) {
                $patchDocument->$var = $value;
            }
        }

        foreach ($sourceDocumentVars as $var => $value) {
            if ($targetDocumentVars === []) {
                $patchDocument->$var = null;
                break;
            }

            if (is_object($value)) {
                if ($sourceDocument->$var !== null && is_object($sourceDocument->$var)) {
                    $subPatch = $this->generate($sourceDocument->$var, $targetDocument->$var);
                    if ($subPatch !== null) {
                        $patchDocument->$var = $subPatch;
                    }
                }
            } elseif (!in_array($var, array_keys($targetDocumentVars))
                      || !in_array($value, array_values($targetDocumentVars))) {
                $sourceDocument->$var = null;
                if (!in_array($var, array_keys($targetDocumentVars))) {
                    $patchDocument->$var = null;
                }
            }
        }

        if (count(get_object_vars($patchDocument)) > 0) {
            return $patchDocument;
        }

        return null;
    }

    /**
     * Merge two patch documents.
     *
     * @param mixed $patchDocument1
     * @param mixed $patchDocument2
     *
     * @return mixed
     */
    public function merge($patchDocument1, $patchDocument2)
    {
        if ($patchDocument1 === null || $patchDocument2 === null
            || !is_object($patchDocument1) || !is_object($patchDocument2)
        ) {
            return $patchDocument2;
        }

        $patchDocument = $patchDocument1;
        $patchDocument1Vars = get_object_vars($patchDocument1);
        $patchDocument2Vars = get_object_vars($patchDocument2);

        foreach ($patchDocument2Vars as $var => $value) {
            if (isset($patchDocument1Vars[$var])) {
                $patchDocument->$var = $this->merge(
                    $patchDocument1->$var,
                    $patchDocument2->$var
                );
            } else {
                $patchDocument->$var = $patchDocument2->$var;
            }
        }

        return $patchDocument;
    }
}
