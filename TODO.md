## priority: high

- [feat] when post created, add new queue job to fetch post's comments

---

## priority: not that high

- [feat] make jobs commandable
- [feat] daily scrape
- [chore] migrate environment to docker?
- [feat] when link was created, fetch their title by http request
- [feat] a clear list indicate which post has no link and already been checkout
    - or remove InvalidLink, just focus NotRegisted site instead
- [refactor] clean up NoMusic class left in code

---

## priority: not sure
- [refactor] make SiteFactory more flexable? (needs evaluate possibility that make all SiteContract method static)
- [test] don't know how to mock Google_Service in PushLinksToGoogleSheet at this moment, maybe fill some test if i have corresponding capability/knowledge in the futrue
