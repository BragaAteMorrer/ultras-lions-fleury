<php

namespace App\Service;

use App\Entity\EventCategory;
use App\Entity\Merch;
use App\Entity\Post;
use App\Entity\Ticket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AutoNewsPublisher
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UrlGeneratorInterface $urlGenerator,
    ) {
    }

    public function publishTicketNews(Ticket $ticket): void
    {
        if ($ticket->getId() === null || !$this->shouldPublishTicket($ticket)) {
            return;
        }

        $slug = sprintf('auto-billetterie-%d', $ticket->getId());
        if ($this->postExists($slug)) {
            return;
        }

        $matchLabel = $ticket->getMatchLabel();
        $url = $this->urlGenerator->generate('ticket_show', ['id' => $ticket->getId()]);
        $date = $ticket->getMatchDate()->format('d/m/Y H:i');
        $description = trim((string) $ticket->getDescription());

        $content = sprintf(
            '<p>La billetterie pour <strong>%s</strong> est disponible.</p>',
            htmlspecialchars($matchLabel, ENT_QUOTES)
        );

        if ($date !== null) {
            $content .= sprintf('<p>Match prevu le %s.</p>', htmlspecialchars($date, ENT_QUOTES));
        }

        if ($description !== '') {
            $content .= sprintf('<p>%s</p>', nl2br(htmlspecialchars($description, ENT_QUOTES)));
        }

        $content .= sprintf('<p><a href="%s">Acceder a la billetterie</a></p>', htmlspecialchars($url, ENT_QUOTES));

        $this->createPost(
            title: sprintf('Billetterie ouverte : %s', $matchLabel),
            slug: $slug,
            content: $content,
            image: $ticket->getImage(),
        );
    }

    public function publishMerchNews(Merch $merch): void
    {
        if ($merch->getId() === null) {
            return;
        }

        $slug = sprintf('auto-matos-%d', $merch->getId());
        if ($this->postExists($slug)) {
            return;
        }

        $title = (string) $merch->getTitle();
        $url = $this->urlGenerator->generate('merch_show', ['id' => $merch->getId()]);
        $description = trim((string) $merch->getDescription());

        $content = sprintf(
            '<p>Nouveau matos disponible : <strong>%s</strong>.</p>',
            htmlspecialchars($title, ENT_QUOTES)
        );

        if ($description !== '') {
            $content .= sprintf('<p>%s</p>', nl2br(htmlspecialchars($description, ENT_QUOTES)));
        }

        $content .= sprintf('<p><a href="%s">Voir le produit</a></p>', htmlspecialchars($url, ENT_QUOTES));

        $this->createPost(
            title: sprintf('Nouveau matos : %s', $title),
            slug: $slug,
            content: $content,
            image: $merch->getImage(),
        );
    }

    private function shouldPublishTicket(Ticket $ticket): bool
    {
        if ($ticket->isArchived()) {
            return false;
        }

        $now = new \DateTimeImmutable();

        return $ticket->isAvailableForUser(true, $now) || $ticket->isAvailableForUser(false, $now);
    }

    private function postExists(string $slug): bool
    {
        return $this->entityManager->getRepository(Post::class)->findOneBy(['slug' => $slug]) !== null;
    }

    private function createPost(string $title, string $slug, string $content, mixed $image): void
    {
        $now = new \DateTime();

        $post = new Post();
        $post
            ->setTitle($title)
            ->setSlug($slug)
            ->setContent($content)
            ->setCreatedAt($now)
            ->setUpdatedAt($now)
            ->setImage($image)
            ->setCategory($this->findArticleCategory());

        $this->entityManager->persist($post);
        $this->entityManager->flush();
    }

    private function findArticleCategory(): EventCategory
    {
        return $this->entityManager->getRepository(EventCategory::class)->findOneBy(
            ['section' => 'articles'],
            ['id' => 'ASC']
        );
    }
}
