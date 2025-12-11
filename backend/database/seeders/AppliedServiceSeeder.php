<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppliedServiceSeeder extends Seeder
{
    public function run(): void
    {
        $posts = DB::table('product_posts')->get();
        $services = DB::table('services')->get();

        if ($posts->isEmpty() || $services->isEmpty())
            return;

        $appliedCount = 0;
        $targetCount = 10;

        foreach ($posts as $post) {
            if ($appliedCount >= $targetCount)
                break;

            $service = $services->random();

            $exists = DB::table('applied_services')
                ->where('post_id', $post->id)
                ->where('service_id', $service->id)
                ->exists();

            if ($exists)
                continue;

            DB::table('applied_services')->insert([
                'post_id' => $post->id,
                'service_id' => $service->id,
                'created_at' => $post->created_at,
                'updated_at' => $post->updated_at,
            ]);

            $appliedCount++;
        }
    }
}
