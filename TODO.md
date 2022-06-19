## priority: high

- [feat] daily scrape

---

## priority: not that high

- [chore] migrate environment to docker?
- [feat] a clear list indicate which post has no link and already been checkout
    - or remove InvalidLink, just focus NotRegisted site instead
- [feat] SearchUser, ThreadPage check current page is greater than `pagenow` or not

  > if the `page` parameter in the query string is greater than the last page, baha will show the first page causing infinite next page

---

## priority: not sure
- [refactor] make SiteFactory more flexable? (needs evaluate possibility that make all SiteContract method static)
- [test] don't know how to mock Google_Service in PushLinksToGoogleSheet at this moment, maybe fill some test if i have corresponding capability/knowledge in the futrue
