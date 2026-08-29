# The credit on client sites

Every site we build should send work back to us. This is the snippet, where it
goes, and the one rule that decides whether it helps or quietly hurts.

## The snippet

Drop into the client site's footer, once, sitewide.

```html
<a href="https://www.fignoc.co.zw/website-design?utm_source=client-site&utm_medium=footer&utm_campaign=built-by"
   rel="noopener"
   style="font-size:.8rem;opacity:.75;text-decoration:none">
  Website by Fignoc
</a>
```

For a client who wants it more discreet, the same link with just `Fignoc` as the
text works. For one happy to be generous, `Website by Fignoc Technologies`.

## The rule that matters

**Keep the anchor text branded. Never make it keyword-rich.**

`Website by Fignoc` is a credit. `Cheap web design Harare` repeated across forty
client footers is a link scheme, and Google's link-spam guidance names
"widely distributed links in the footers of various sites" as an example. The
punishment is not usually a penalty — it is that the links stop counting, so
you would have spent the goodwill for nothing.

Branded credits are normal, expected, and they work. Keyword-stuffed ones are
the thing that gets devalued. There is no version of this worth being clever
about.

## Why it points at `/website-design`

A visitor clicking a footer credit has just been on a site they liked and is
wondering what it cost. `/website-design` answers exactly that — published
prices, the guarantee, and a form. The homepage would make them hunt.

## Why the UTM parameters

Without them every one of these arrives in GA4 as a plain referral and you
cannot tell the channel apart from any other link. With them you can answer
"how many enquiries did client footers bring last quarter", which is the only
way to know whether this is worth maintaining.

They do not create a duplicate-content problem: the layout's canonical strips
query strings, so `?utm_source=…` always canonicalises to the clean URL.

**This only reports once GA4 is configured.** `config('fignoc.analytics.ga4')`
is still `null`, so right now the parameters are recorded by nobody.

## Ask for it in the contract, not after launch

The credit is easiest to place while you are building. Put a line in the
proposal — a small footer credit, removable on request — and it is settled
before anyone has an opinion about it. Chasing it after handover is a
conversation nobody enjoys.

If a client asks for it removed, remove it. A credit on a site whose owner
resents it is worth less than the relationship.

## What this is worth

Two things, and the second is the bigger one:

1. **Referral traffic** — people who just saw your work and want the same.
2. **Links from real Zimbabwean business domains.** For a local agency this is
   the most defensible ranking asset there is, because competitors cannot buy
   it. It is the slow compounding kind: forty client sites over two years does
   more for `web design company Harare` than any amount of on-page work.

## Related

- Target page: [`/website-design`](https://www.fignoc.co.zw/website-design)
- Keyword context: [SEO-KEYWORDS.md](SEO-KEYWORDS.md)
