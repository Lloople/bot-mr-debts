<?php

namespace App\Conversations;

use App\Models\Group;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;
use App\Traits\HasCurrency;

class RegisterGroupConversation extends Conversation
{

    use HasCurrency;

    /** @var string */
    protected $language;

    /** @var string */
    protected $currency;

    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askLanguage();
    }

    public function askLanguage()
    {
        $this->language = $this->bot->getUser()->getInfo()['language_code'] ?? app()->getLocale();

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

            app()->setLocale($this->language);

            $this->currency = $answer->getValue();

            $this->say(trans('groups.new_group_created'));

            $group = Group::updateOrCreateFromChat(
                collect($this->bot->getMessage()->getPayload())->get('chat'),
                $this->language,
                $this->currency
            );
        });
    }

    protected function getQuestionLanguage()
    {
        return Question::create(trans('groups.ask_language'))
            ->addButtons([
                Button::create('Català')->value('ca'),
                Button::create('Castellano')->value('es'),
                Button::create('English')->value('en'),
            ]);
    }

    protected function getQuestionCurrency()
    {
        return Question::create(trans('groups.ask_currency'))
            ->addButtons($this->getCurrenciesAsButtons());
    }

    protected function getCurrenciesAsButtons()
    {
        return array_map(function ($symbol, $currency) {
            return Button::create($symbol)->value($currency);
        }, $this->currency_symbols, array_keys($this->currency_symbols));
    }
}
