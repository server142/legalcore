<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// 1. Drop table if exists
Schema::dropIfExists('directory_profiles');

// 2. Clear migration record
DB::table('migrations')->where('migration', 'like', '%create_directory_profiles_table%')->delete();

echo "Table dropped and migration record cleared.\n";
