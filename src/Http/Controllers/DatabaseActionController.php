<?php
    
    namespace Protoqol\Prequel\Http\Controllers;
    
    use Exception;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Protoqol\Prequel\App\ModelAction;
    use Protoqol\Prequel\App\SeederAction;
    use Protoqol\Prequel\App\FactoryAction;
    use Illuminate\Routing\Controller;
    use Illuminate\Support\Facades\Artisan;
    use Protoqol\Prequel\App\AppStatus;
    use Protoqol\Prequel\App\MigrationAction;
    use Protoqol\Prequel\Facades\PDB;
    use Protoqol\Prequel\App\ResourceAction;
    use Protoqol\Prequel\Traits\classResolver;
    use Protoqol\Prequel\App\ControllerAction;
    use Protoqol\Prequel\Database\DatabaseAction;
    use phpDocumentor\Reflection\DocBlock\Tags\See;
    use Protoqol\Prequel\Database\DatabaseTraverser;
    
    /**
     * Class DatabaseActionController
     * @package Protoqol\Prequel\Http\Controllers
     */
    class DatabaseActionController extends Controller
    {
        
        /**
         * Get defaults for 'Insert new row' action form inputs.
         *
         * @param string $database
         * @param string $table
         *
         * @return array
         */
        public function getDefaultsForTable(string $database, string $table): array
        {
            return [
                'id'           => ((int)PDB::create($database, $table)->count() + 1),
                'current_date' => Carbon::now()->format('Y-m-d\TH:i'),
            ];
        }
        
        /**
         * Check and return all Laravel specific assets for table (Model, Seeder, Controller etc.).
         *
         * @param string $database
         * @param string $table
         *
         * @return array
         * @throws \Exception
         */
        public function getInfoAboutTable(string $database, string $table): array
        {
            return [
                'controller' => (new ControllerAction($database, $table))->getQualifiedName(),
                'resource'   => (new ResourceAction($database, $table))->getQualifiedName(),
                'model'      => (new ModelAction($database, $table))->getQualifiedName(),
                'seeder'     => (new SeederAction($database, $table))->getQualifiedName(),
                'factory'    => (new FactoryAction($database, $table))->getQualifiedName(),
            ];
        }
        
        /**
         * Insert row in table.
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return array
         */
        public function insertNewRow(Request $request): array
        {
            try {
                $res = (new DatabaseAction($request->database, $request->table))->insertNewRow($request->post('data'));
            } catch (Exception $e) {
                throw new $e;
            }
            
            return [
                'success' => $res,
            ];
        }
        
        /**
         * Run raw SQL query.
         *
         * @param string $database
         * @param string $table
         * @param string $query
         *
         * @return string
         */
        public function runSql(string $database, string $table, string $query): string
        {
            return (string)PDB::create($database, $table)->statement($query);
        }
        
        /**
         * @param string $database
         * @param string $table
         */
        public function import(string $database, string $table)
        {
            //
        }
        
        /**
         * @param string $database
         * @param string $table
         */
        public function export(string $database, string $table)
        {
            //
        }
        
        /**
         * Get database status.
         * @return array
         */
        public function status()
        {
            return (new AppStatus())->getStatus();
        }
        
        /**
         * Run pending migrations.
         *
         * @param string $database
         * @param string $table
         *
         * @return int
         */
        public function runMigrations(string $database, string $table)
        {
            return (new MigrationAction($database, $table))->run();
        }
        
        /**
         * Reset latest migrations.
         *
         * @param string $database
         * @param string $table
         *
         * @return int
         */
        public function resetMigrations(string $database, string $table)
        {
            return (new MigrationAction($database, $table))->reset();
        }
        
        /**
         * Generate controller.
         *
         * @param string $database
         * @param string $table
         *
         * @return mixed
         * @throws \Exception
         */
        public function generateController(string $database, string $table)
        {
            return (new ControllerAction($database, $table))->generate();
        }
        
        /**
         * Generate factory.
         *
         * @param string $database
         * @param string $table
         *
         * @return int|string
         * @throws \Exception
         */
        public function generateFactory(string $database, string $table)
        {
            return (new FactoryAction($database, $table))->generate();
        }
        
        /**
         * Generate model.
         *
         * @param string $database
         * @param string $table
         *
         * @return int
         */
        public function generateModel(string $database, string $table)
        {
            return (new ModelAction($database, $table))->generate();
        }
        
        /**
         * Generate resource.
         *
         * @param string $database
         * @param string $table
         *
         * @return mixed
         * @throws \Exception
         */
        public function generateResource(string $database, string $table)
        {
            return (new ResourceAction($database, $table))->generate();
        }
        
        /**
         * Generate seeder.
         *
         * @param string $database
         * @param string $table
         *
         * @return int|string
         * @throws \Exception
         */
        public function generateSeeder(string $database, string $table)
        {
            return (new SeederAction($database, $table))->generate();
        }
        
        /**
         * Run seeder.
         *
         * @param string $database
         * @param string $table
         *
         * @return int
         * @throws \Exception
         */
        public function runSeeder(string $database, string $table)
        {
            return (new SeederAction($database, $table))->run();
        }
    }
