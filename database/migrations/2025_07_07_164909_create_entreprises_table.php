<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('entreprises', function (Blueprint $table) {
       $table->id();
            $table->string('nom_entreprise', 255);
            $table->string('code_ice', 50)->nullable()->unique()->default(NULL);
            $table->string('rc', 50)->nullable()->unique()->default(NULL);


            $table->enum('forme_juridique', ['SA', 'SARL', 'SNC', 'SCS', 'autre'])->nullable()->default(NULL);
            $table->enum('type', ['PP', 'PM'])->nullable()->default(NULL);
            $table->enum('taille_entreprise', ['PME', 'GE', 'SU'])->nullable()->default(NULL);
            $table->enum('en_activite', ['oui', 'non'])->nullable()->default('oui');


            $table->text('adresse');
            $table->string('ville', 255)->nullable()->default(NULL);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();


            $table->text('secteur')->nullable()->default(NULL);
            $table->string('activite', 255)->nullable()->default(NULL);
            $table->text('certifications')->nullable()->default(NULL);


            $table->string('email', 255)->nullable()->default(NULL);
            $table->string('tel', 20)->nullable()->default(NULL);
            $table->string('fax', 50)->nullable()->default(NULL);
            $table->string('contact', 255)->nullable()->default(NULL);
            $table->string('site_web', 255)->nullable()->default(NULL);


            $table->string('cnss', 50)->nullable()->default(NULL);
            $table->string('if', 50)->nullable()->default(NULL);
            $table->string('patente', 50)->nullable()->default(NULL);


            $table->timestamp('date_creation')->useCurrent();
            $table->timestamps();

            $table->index('code_ice')->default(NULL);
            $table->index('ville')->default(NULL);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprises');
    }
};
