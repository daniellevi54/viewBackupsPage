<?php
abstract Class Controller
{
    protected function getLaundryID(){

    }

    protected function view(string $view, array $data = []){
        extract($data);

        require __DIR__ . '/../views/' . $view . '.php';
    }

    private function getObjects($id){
            
        if($id === null){
            return null;
        }

        $path = $_SESSION['path'] ?? ''; 
        $prefix = $id . "/" . $path;

        $S3Client = new S3Client;
        return $S3Client->callService('list', $prefix);
    }

    private function treatObjects($id){
            $objects = $this->getObjects($id);

            if(!$objects){
                return ['folders' => [], 'files' => []];
            }

            $folders = $objects['CommonPrefixes'] ?? [];
            if (isset($folders['Prefix'])){
                $folders = [$folders];
            }

            $files = $objects['Contents'] ?? [];
            if (isset($files['Key'])) {
                $files = [$files];
            }

            $files = array_values(array_filter($files, function ($file) {
                return !(substr($file['Key'], -1) === '/' && $file['Size'] == 0);
            }));

            return ['folders' => $folders, 'files' => $files];
        }

        private function stripLaundryPrefix(array $objects, string $id): array{
            
            $toStrip = $id . '/';

            $objects['folders'] = array_map(function ($folder) use ($toStrip) {
                $folder['Prefix'] = $this->removePrefix($folder['Prefix'], $toStrip);
                return $folder;
            }, $objects['folders']);

            $objects['files'] = array_map(function ($file) use ($toStrip) {
                $file['Key'] = $this->removePrefix($file['Key'], $toStrip);
                return $file;
            }, $objects['files']);

            return $objects;
        }

        private function removePrefix(string $value, string $prefix): string{
            if (substr($value, 0, strlen($prefix)) === $prefix) {
            return substr($value, strlen($prefix));
            }
            return $value;
        }

        protected function parentPath(string $path): string{
            $path = rtrim($path, '/');

            if($path === ''){
                return '';
            }

            $lastSlash = strrpos($path, '/');

            if($lastSlash === false){
                return '';
            }

            return substr($path, 0, $lastSlash + 1);
        }
    
        private function addDisplayNames(array $objects): array
        {
            $objects['folders'] = array_map(function ($folder) {
                $folder['name'] = $this->folderDisplayName($folder['Prefix']);
                return $folder;
            }, $objects['folders']);

            $objects['files'] = array_map(function ($file) {
                $file['name'] = basename($file['Key']);
                return $file;
            }, $objects['files']);
            
            return $objects; 
        }

        private function folderDisplayName(string $prefix): string
        {
            return basename(rtrim($prefix, '/')) . '/';
        }

        protected function getDisplayableObjects(?string $id): array{
            $objects = $this->treatObjects($id);

            if ($id !== null) {
               $objects = $this->stripLaundryPrefix($objects, $id);
               $objects = $this->addDisplayNames($objects);
               rsort($objects['folders'], SORT_DESC);
               rsort($objects['files'], SORT_DESC);
            }

            return $objects;
        }

        public function download(){
            $authService = new AuthService;
            
            if (!$authService->checkAuthentication()){
                http_response_code(403);
                exit("Não Autorizado");
            }            

            $id = $this->getLaundryID();
            $key = $_GET['Key'];
            if ($key === ''){
                http_response_code(400);
                exit("Arquivo não especificado");
            }

            if($id === null){
                http_response_code(403);
                exit('Não Autorizado');
            }
            
            $fullKey = $id . '/' . $key;

            $S3Client = new S3Client;
            $content = $S3Client->callService('download', $fullKey);
            if (!$content){
                http_response_code(400);
                exit("Arquivo não encontrado");
            }

            $filename = basename($key);

            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . strlen($content));
            echo $content;
        }
}

?>