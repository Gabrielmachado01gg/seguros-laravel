<?php

use Illuminate\Support\Facades\Schedule;

// Aviso diário de apólices perto de vencer (e-mail + WhatsApp).
Schedule::command('apolices:avisar-vencimento')->dailyAt('09:00')->withoutOverlapping();
