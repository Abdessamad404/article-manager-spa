<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Article;
use App\Models\User;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        // Récupérer nos utilisateurs de test
        $writer = User::where('email', 'writer@test.com')->first();
        $editor = User::where('email', 'editor@test.com')->first();

        $articles = [
            [
                'title' => 'La révolution de l\'intelligence artificielle dans l\'industrie',
                'content' => 'L\'intelligence artificielle transforme radicalement le paysage industriel moderne. Les entreprises adoptent massivement ces technologies pour optimiser leurs processus de production, réduire les coûts et améliorer la qualité. Cette révolution technologique ouvre de nouvelles perspectives d\'innovation et de croissance.',
                'category' => 'Technology',
                'status' => 'approved',
                'author_id' => $writer->id,
                'image' => 'images/image.jpeg', // Ajoutez des images fictives
            ],
            [
                'title' => 'Économie française : perspectives pour 2025',
                'content' => 'L\'économie française montre des signes de reprise après une période difficile. Les indicateurs macroéconomiques suggèrent une croissance modérée mais stable pour l\'année 2025. Les secteurs du numérique et de l\'énergie verte tirent particulièrement leur épingle du jeu.',
                'category' => 'Économie',
                'status' => 'approved',
                'author_id' => $editor->id,
            ],
            [
                'title' => 'Innovation dans les énergies renouvelables',
                'content' => 'Les dernières innovations technologiques dans le domaine des énergies renouvelables promettent de révolutionner notre approche de la production énergétique. Panneaux solaires nouvelle génération, éoliennes offshore et systèmes de stockage avancés constituent les piliers de cette transformation.',
                'category' => 'Innovation',
                'status' => 'pending',
                'author_id' => $writer->id,
                'image' => 'images/image.jpeg', // Ajoutez des images fictives
            ],

            [
                'title' => 'L\'industrie automobile face aux défis de la transition',
                'content' => 'L\'industrie automobile traverse une période de mutation sans précédent. Entre électrification, automatisation et nouvelles mobilités, les constructeurs doivent repenser entièrement leurs modèles économiques et leurs chaînes de production.',
                'category' => 'Industrie',
                'status' => 'draft',
                'author_id' => $writer->id,
                'image' => 'images/image.jpeg', // Ajoutez des images fictives
            ],

            [
                'title' => 'Nouvelles réglementations européennes sur la tech',
                'content' => 'L\'Union européenne renforce son arsenal réglementaire concernant les grandes plateformes technologiques. Ces nouvelles mesures visent à protéger les consommateurs et à garantir une concurrence équitable dans le secteur numérique.',
                'category' => 'Politique',
                'status' => 'approved',
                'author_id' => $editor->id,
            ],

            [
                'title' => 'Start-ups françaises : un écosystème en pleine expansion',
                'content' => 'L\'écosystème des start-ups françaises n\'a jamais été aussi dynamique. Avec des levées de fonds record et l\'émergence de nouvelles licornes, la France confirme sa position de leader européen dans l\'innovation technologique.',
                'category' => 'Innovation',
                'status' => 'rejected',
                'author_id' => $writer->id,
                'image' => 'images/image.jpeg', // Ajoutez des images fictives
            ],

            [
                'title' => 'Digital nomadisme : impact sur l\'économie locale',
                'content' => 'Le phénomène du digital nomadisme transforme l\'économie de nombreuses régions. Cette nouvelle forme de travail à distance génère des opportunités économiques inédites tout en posant des défis d\'infrastructure et de régulation.',
                'category' => 'Économie',
                'status' => 'pending',
                'author_id' => $editor->id,
            ],

            [
                'title' => 'Robotique industrielle : vers une production 100% automatisée',
                'content' => 'La robotique industrielle atteint un niveau de sophistication permettant d\'envisager des usines entièrement automatisées. Cette évolution soulève des questions importantes sur l\'avenir de l\'emploi industriel et la formation des travailleurs.',
                'category' => 'Industrie',
                'status' => 'approved',
                'author_id' => $writer->id,
                'image' => 'images/image.jpeg', // Ajoutez des images fictives
            ]

        ];

        foreach ($articles as $articleData) {
            Article::insert($articleData);
        }

        $this->command->info('Articles crées avec succès.');
    }
}