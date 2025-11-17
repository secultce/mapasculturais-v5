<?php
namespace CounterReason\Traits;

use MapasCulturais\App;
use MapasCulturais\Exceptions\FileUploadError;

trait FileUploadTrait
{
    /**
     * Upload genérico com validação, grupo, dono e privacidade
     */
    protected function uploadFiles(
        $owner,
        array $fileGroups = ['cr-attachment'],
        array $descriptions = [],
        bool $uniquePerGroup = false
    ): array {
        $app = App::i();
        $result = [];
        $files = [];

        if (empty($_FILES)) {
            return $result;
        }
      
        foreach (array_keys($_FILES) as $group_name) {
            
            // Só processa grupos permitidos
            if (!in_array($group_name, $fileGroups)) {
                continue;
            }

            $upload_group = $app->getRegisteredFileGroup($this->id ?? get_class($owner), $group_name);
            if (!$upload_group) {
                $files[] = ['error' => 'Grupo de arquivo não configurado', 'group' => $group_name];
                continue;
            }

            try {
                $file = $app->handleUpload($group_name, $this->getFileClassName());
               
                if (is_array($file)) {
                    foreach ($file as $f) {
                        $this->processUploadedFile($f, $owner, $group_name, $descriptions, $files, $upload_group, $uniquePerGroup);
                    }
                } else {
                    $this->processUploadedFile($file, $owner, $group_name, $descriptions, $files, $upload_group, $uniquePerGroup);
                }
            } catch (FileUploadError $e) {
                $files[] = ['error' => $e->getMessage(), 'group' => $upload_group];
            }
        }

        // Remove erros, mantém apenas arquivos válidos
        foreach ($files as $f) {
            if (is_object($f)) {
                $file_group = $f->group;
                if ($uniquePerGroup && isset($result[$file_group])) {
                    $result[$file_group]->delete();
                }
                $f->save();
                if ($uniquePerGroup) {
                    $result[$file_group] = $f;
                } else {
                    $result[$file_group][] = $f;
                }
            }
        }

        return $result;
    }

    private function processUploadedFile(
        $file,
        $owner,
        $group_name,
        array $descriptions,
        array &$files,
        $upload_group,
        bool $uniquePerGroup
    ): void {
        if ($errors = $file->getValidationErrors()) {
            $error_messages = [];
            foreach ($errors as $_errors) {
                $error_messages = array_merge(array_values($_errors), $error_messages);
            }
            $files[] = ['error' => implode(', ', $error_messages), 'group' => $upload_group];
            return;
        }

        if ($error = $upload_group->getError($file)) {
            $files[] = ['error' => $error, 'group' => $upload_group];
            return;
        }

        if (isset($descriptions[$group_name])) {
            $file->description = $descriptions[$group_name];
        }

        $file->owner = $owner;
        $file->private = true;
        $file->group = $group_name;

        $files[] = $file;
    }

    /**
     * Retorna o nome da classe File do módulo
     */
    abstract protected function getFileClassName(): string;
}