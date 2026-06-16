<?php

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Contact')]
#[Layout('layouts.marketing')]
class extends Component
{
}; ?>

<main class="bg-[#0a0a0c] text-white">
    <section wire:ignore class="relative overflow-hidden px-6 pb-24 pt-40 sm:px-10 lg:px-20" style="background: radial-gradient(ellipse at 15% 85%, rgba(54,107,195,0.18) 0%, #0a0a0c 60%);">
        <div class="pointer-events-none absolute -right-15 -top-15 h-[400px] w-[400px] bg-[radial-gradient(ellipse,rgba(230,0,18,0.15)_0%,transparent_65%)]"></div>
        <div class="relative z-10 mx-auto max-w-[1800px]">
            <p class="mb-4 text-xs font-semibold uppercase tracking-[0.26em] text-white/35" data-hero-reveal style="--hero-delay: 100ms; --hero-duration: 700ms; --hero-y: 18px;">Let's Talk</p>
            <h1 class="max-w-none text-[clamp(3rem,7vw,5.5rem)] font-black uppercase leading-none tracking-[-0.03em] lg:whitespace-nowrap" data-hero-reveal style="--hero-delay: 250ms; --hero-duration: 850ms; --hero-y: 28px;">
                <span class="bg-linear-to-r from-[#366bc3] via-[#6d55a5] to-[#823665] bg-clip-text text-transparent">Ready to Scale </span><span class="bg-linear-to-r from-[#823665] via-[#b4143c] to-[#e60012] bg-clip-text text-transparent">Your Vision?</span>
            </h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-white/48" data-hero-reveal style="--hero-delay: 450ms; --hero-duration: 800ms; --hero-y: 20px;">It all starts with a conversation. Let us explore how the Vidhya Studio engine can support your creative strategy, elevate your visual storytelling, and drive measurable results.</p>
        </div>
    </section>

    <section class="px-6 py-20 sm:px-10 lg:px-20">
        <div class="mx-auto grid max-w-[1800px] gap-16 lg:grid-cols-[1fr_1.5fr] lg:gap-24">
            <aside wire:ignore>
                <div class="lg:sticky lg:top-28">
                    <div class="mb-12" data-reveal="left" style="--reveal-delay: 80ms;">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-5 w-5 shrink-0 items-center justify-center border border-[#366bc3] bg-[#366bc3]/10">
                                <div class="h-1.5 w-1.5 bg-[#366bc3]"></div>
                            </div>
                            <h2 class="text-base font-black uppercase tracking-[0.04em]">Start a Project</h2>
                        </div>
                        <p class="text-sm leading-8 text-white/42">Fill out the form to share your goals and requirements. We will schedule a strategic call to discuss how we can best support your specific creative needs.</p>
                    </div>

                    <div class="mb-12" data-reveal="left" style="--reveal-delay: 160ms;">
                        <div class="mb-4 flex items-center gap-3">
                            <div class="flex h-5 w-5 shrink-0 items-center justify-center border border-[#e60012] bg-[#e60012]/10">
                                <div class="h-1.5 w-1.5 bg-[#e60012]"></div>
                            </div>
                            <h2 class="text-base font-black uppercase tracking-[0.04em]">Direct Email</h2>
                        </div>
                        <p class="text-sm leading-8 text-white/42">Prefer to use your own email and send reference attachments? Reach out to us directly.</p>
                        <a href="mailto:hello@vidhyastudio.com" class="mt-3 inline-block bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-sm font-semibold text-transparent hover:opacity-80 transition-opacity">hello@vidhyastudio.com</a>
                    </div>

                    <div class="mb-10 h-[3px] w-full bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]" data-reveal="left" style="--reveal-delay: 240ms;"></div>

                    <div class="border-l-[3px] border-white/8 bg-[#0d0d13] p-6" data-reveal="left" style="--reveal-delay: 320ms;">
                        <p class="text-xs leading-7 text-white/35">Vidhya Studio is a new venture from <span class="font-semibold text-white/60">Benetone Films</span>. We work alongside Benetone Advertising and Benetone Originals to offer a full creative ecosystem.</p>
                    </div>
                </div>
            </aside>

            <div wire:ignore class="contact-form-enter" data-contact-shell>
                <div class="hidden min-h-[290px] items-center justify-center border border-[#366bc3]/20 bg-[#0f0f18] px-8 py-16 text-center" data-contact-success>
                    <div>
                        <h2 class="mb-6 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012] bg-clip-text text-[clamp(2rem,4vw,2.75rem)] font-black uppercase leading-tight tracking-[-0.02em] text-transparent">Message Received.</h2>
                        <p class="mx-auto mb-10 max-w-2xl text-sm leading-8 text-white/58">Thank you for reaching out. We'll review your brief and be in touch within 24 hours to schedule your strategic call.</p>
                        <div class="mx-auto h-[3px] w-15 bg-linear-to-r from-[#366bc3] via-[#823665] to-[#e60012]"></div>
                    </div>
                </div>

                <form class="space-y-3" novalidate data-contact-form>
                    <div class="grid gap-3 md:grid-cols-2">
                        <input name="name" data-contact-field="name" placeholder="Full Name" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]">
                        <input name="email" data-contact-field="email" placeholder="Email Address" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]">
                    </div>
                    <input name="company" placeholder="Company / Brand" class="w-full rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]">

                    <div class="relative w-full" data-contact-service>
                        <input type="hidden" name="service" data-contact-service-input>
                        <button type="button" class="flex w-full items-center justify-between rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-left text-sm text-white outline-none transition focus:border-[#366bc3]" data-contact-service-toggle aria-expanded="false" aria-haspopup="listbox">
                            <span class="text-white/28" data-contact-service-label>Service Needed</span>
                            <svg class="h-4.5 w-4.5 text-white/40 transition-transform duration-200" data-contact-service-icon fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div class="vidhya-contact-service-menu absolute left-0 z-50 mt-1 w-full max-h-[380px] overflow-y-auto rounded border border-white/10 bg-[#0c0c12] py-1 text-sm text-white shadow-2xl" data-contact-service-menu role="listbox">
                            <button type="button" class="block w-full px-4 py-2.5 text-left text-white/35 transition-colors hover:bg-white/8" data-contact-service-option value="" role="option" aria-selected="true" data-selected>Service Needed</button>
                            @foreach (['AI POCs & Previs', 'AI Advertising', 'Post Production', 'AI Models & Influencers', 'AI Marketing Content', 'Micro Drama', 'Training & Workshop', 'Strategic Consulting', 'Not Sure Yet'] as $option)
                                <button type="button" class="block w-full px-4 py-2.5 text-left transition-colors hover:bg-white/8" data-contact-service-option value="{{ $option }}" role="option" aria-selected="false">{{ $option }}</button>
                            @endforeach
                        </div>
                    </div>

                    <textarea name="message" data-contact-field="message" rows="6" placeholder="Tell us about your project - goals, challenges, timeline, references..." class="w-full resize-y rounded border border-white/10 bg-white/[0.04] px-4 py-3.5 text-sm text-white outline-none transition placeholder:text-white/28 focus:border-[#366bc3]"></textarea>

                    <button type="submit" class="w-full rounded px-8 py-4 text-sm font-bold uppercase tracking-[0.1em] transition hover:brightness-110 disabled:cursor-wait disabled:opacity-70" style="background: linear-gradient(90deg, #366bc3, #823665, #e60012);" data-contact-submit>
                        <span data-contact-submit-label>Send Message</span>
                    </button>
                    <p class="pt-1 text-center text-xs text-white/22">We respond within 24 hours. Your information is kept strictly confidential.</p>
                </form>
            </div>
        </div>
    </section>
</main>
