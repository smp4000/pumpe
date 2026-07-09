<?php

declare(strict_types=1);

// Die Startseite leitet auf das App-Panel weiter
it('leitet die Startseite auf das App-Panel weiter', function (): void {
    $this->get('/')->assertRedirect('/app');
});
