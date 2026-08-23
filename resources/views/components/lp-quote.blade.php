{{--
  "Get started" dialog for the landing page.

  Every Get started button on the page opens this instead of jumping straight to
  WhatsApp — a cold tap into a chat app leaves the visitor to compose the
  enquiry themselves, and most don't. The form asks the six things we need to
  quote, pre-selects the package they clicked from, and still offers WhatsApp as
  a one-tap alternative for anyone who'd rather talk.

  Native <dialog>: focus trapping, Escape and the backdrop come free.
--}}
@props(['packages' => [], 'wa' => null])

@php
    $budgets = ['Under $150', '$150 – $320', '$320 – $600', '$600 – $1,500', 'Over $1,500', 'Not sure yet'];
@endphp

<dialog class="lp-modal" id="lp-quote" aria-labelledby="lp-quote-h"
        data-auto-open="{{ session('website_enquiry') ? 'true' : 'false' }}">
@if (session('website_enquiry'))
    {{-- Posted and delivered: confirm in place rather than sending them off to
         hunt for a thank-you page. --}}
    <div class="lp-modal-card">
        <div class="lp-modal-done">
            <span class="lp-modal-done-ico" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12.5 9 17.5 20 6.5"/></svg>
            </span>
            <h2 id="lp-quote-h" class="lp-modal-title">Got it. Talk soon.</h2>
            <p class="lp-modal-sub">We've read your enquiry and we reply within one business day, on the
            number you gave us.</p>
            <button type="button" class="lp-btn lp-btn--quiet" data-quote-close>Close</button>
        </div>
    </div>
@else
    <form method="POST" action="{{ route('landing.website.enquiry') }}" class="lp-modal-card" id="lp-quote-form"
          data-wa="{{ $wa }}">
        @csrf

        <div class="lp-modal-head">
            <div>
                <h2 id="lp-quote-h" class="lp-modal-title">Let’s scope your website</h2>
                <p class="lp-modal-sub">Three answers is enough to start. We reply within one business day with
                a straight answer on what fits and what it costs.</p>
            </div>
            <button type="button" class="lp-modal-x" data-quote-close aria-label="Close">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg>
            </button>
        </div>

        <div class="lp-modal-body">
            <div class="lp-field">
                <label for="q-name">Your name <span aria-hidden="true">*</span></label>
                <input id="q-name" name="name" type="text" required autocomplete="name" maxlength="100">
            </div>

            <div class="lp-field">
                <label for="q-business">Business name <span class="lp-field-opt">optional</span></label>
                <input id="q-business" name="business" type="text" autocomplete="organization" maxlength="120">
            </div>

            <div class="lp-field">
                <label for="q-phone">WhatsApp number <span aria-hidden="true">*</span></label>
                <input id="q-phone" name="phone" type="tel" required autocomplete="tel"
                       inputmode="tel" placeholder="077 000 0000" maxlength="30">
            </div>

            <div class="lp-field">
                <label for="q-email">Email <span class="lp-field-opt">optional</span></label>
                <input id="q-email" name="email" type="email" autocomplete="email" maxlength="150">
            </div>

            <div class="lp-field lp-field--wide">
                <label for="q-website">Website to analyse <span class="lp-field-opt">optional — leave blank if you don’t have one</span></label>
                <input id="q-website" name="website" type="text" inputmode="url"
                       placeholder="yourbusiness.co.zw" maxlength="200">
            </div>

            <div class="lp-field">
                <label for="q-package">What kind of website</label>
                <select id="q-package" name="package">
                    @foreach ($packages as $p)
                        <option value="{{ $p['name'] }}">{{ $p['name'] }} — ${{ $p['price'] }} {{ $p['unit'] }}</option>
                    @endforeach
                    <option value="Online store">Online store</option>
                    <option value="Booking system">Booking system</option>
                    <option value="Not sure yet" selected>Not sure yet — advise me</option>
                </select>
            </div>

            <div class="lp-field">
                <label for="q-budget">Budget <span class="lp-field-opt">optional</span></label>
                <select id="q-budget" name="budget">
                    <option value="">Prefer not to say</option>
                    @foreach ($budgets as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>

            <div class="lp-field lp-field--wide">
                <label for="q-goal">What do you need it to do? <span aria-hidden="true">*</span></label>
                <textarea id="q-goal" name="goal" rows="3" required minlength="10" maxlength="2000"
                          placeholder="More enquiries, sell online, take bookings, show our prices…"></textarea>
            </div>

            {{-- Honeypot. Hidden from people, irresistible to bots; the server
                 rejects the request outright when it arrives filled. --}}
            <div class="lp-hp" aria-hidden="true">
                <label for="q-company-url">Company URL</label>
                <input id="q-company-url" name="company_url" type="text" tabindex="-1" autocomplete="off">
            </div>
        </div>

        <div class="lp-modal-foot">
            <button type="submit" class="lp-btn lp-btn--primary">Send enquiry</button>
            @if ($wa)
                <button type="button" class="lp-btn lp-btn--quiet" data-quote-wa>
                    <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 2.1.55 4.05 1.6 5.77L2 22l4.45-1.17a9.86 9.86 0 0 0 5.59 1.72c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm4.52 12.99c-.25-.12-1.47-.72-1.69-.81-.23-.08-.39-.12-.56.13-.16.25-.64.8-.78.97-.14.16-.29.18-.54.06-.25-.12-1.05-.39-1.99-1.23-.74-.66-1.23-1.47-1.38-1.72-.14-.25-.01-.38.11-.5.11-.11.25-.29.37-.43.12-.14.16-.25.25-.41.08-.16.04-.31-.02-.43-.06-.12-.56-1.34-.76-1.84-.2-.48-.4-.42-.56-.43h-.47c-.16 0-.43.06-.65.31-.22.25-.86.84-.86 2.05 0 1.21.88 2.38 1 2.54.12.16 1.73 2.64 4.19 3.7.59.25 1.04.4 1.4.51.59.19 1.12.16 1.54.1.47-.07 1.47-.6 1.68-1.18.21-.58.21-1.07.14-1.18-.06-.11-.22-.17-.47-.29Z"/></svg>
                    Send on WhatsApp instead
                </button>
            @endif
            <p class="lp-modal-note">No spam, no mailing list. We reply once, to the number you gave us.</p>
        </div>
    </form>
@endif
</dialog>
