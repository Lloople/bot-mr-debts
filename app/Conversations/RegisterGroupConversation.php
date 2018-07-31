<?php

namespace App\Conversations;

use App\Models\Group;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class RegisterGroupConversation extends Conversation
{

    private $language;
    private $currency;

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askLanguage();
    }

    public function askLanguage()
    {
        $this->language = $this->bot->getUser()->getInfo()['language_code'] ?? 'es';

        app()->setLocale($this->language);

        $this->say(trans('groups.new_group_greetings'));

        return $this->ask($this->getQuestionLanguage(), function (Answer $answer) {
            if (! $answer->isInteractiveMessageReply()) {
                return;
            }

            $this->language = $answer->getValue();

            return $this->askCurrency();
        });
    }

    public function askCurrency()
    {
        app()->setLocale($this->language);

        return $this->ask($this->getQuestionCurrency(), function (Answer $answer) {

            if (! $answer->isInteractiveMessageReply()) {
                return;
            }

            $this->currency = $answer->getValue();

            $this->say(trans('groups.new_group_setted', [
                'language' => $this->language,
                'currency' => $this->currency
            ]));

            Group::createFromChat(
                collect($this->bot->getMessage()->getPayload())->get('chat'),
                $this->language,
                $this->currency
            );
        });
    }

    private function getQuestionLanguage()
    {
        return Question::create(trans('groups.ask_language'))
            ->callbackId('ask_language')
            ->addButtons([
                Button::create('Català')->value('ca'),
                Button::create('Castellano')->value('es'),
                Button::create('English')->value('en'),
            ]);
    }

    private function getQuestionCurrency()
    {
        return Question::create(trans('groups.ask_currency'))
            ->callbackId('ask_currency')
            ->addButtons([
                Button::create('€')->value('eur'),
                Button::create('$')->value('usd'),
                Button::create('£')->value('gbp'),
            ]);
    }
}
