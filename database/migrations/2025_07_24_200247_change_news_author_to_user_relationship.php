<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\News;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the new author_id column
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable()->after('images')->constrained('users')->onDelete('cascade');
        });

        // Migrate existing data - set the first user as author for existing news
        $firstUser = User::first();
        if ($firstUser) {
            News::whereNull('author_id')->update(['author_id' => $firstUser->id]);
        }

        // Make author_id required after data migration
        Schema::table('news', function (Blueprint $table) {
            $table->foreignId('author_id')->nullable(false)->change();
        });

        // Remove the old author column
        Schema::table('news', function (Blueprint $table) {
            $table->dropColumn('author');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the author column
        Schema::table('news', function (Blueprint $table) {
            $table->string('author')->after('images');
        });

        // Migrate data back - use the user name as author
        News::with('author')->chunk(100, function ($newsItems) {
            foreach ($newsItems as $news) {
                $news->update(['author' => $news->author->name ?? 'Unknown']);
            }
        });

        // Drop the author_id foreign key and column
        Schema::table('news', function (Blueprint $table) {
            $table->dropForeign(['author_id']);
            $table->dropColumn('author_id');
        });
    }
};
