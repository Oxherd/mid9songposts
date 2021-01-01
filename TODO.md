## priority: high

- when scrape **single post** from different year, thread's date might be incorrect.  
    for example:  
    ```
    thread's title is "12/31" and published in "2020"  
    and post published in "2021-01-01",  
    the thread's date should be "2020-12-31", but "2021-12-31" instead.
    ```

---

## priority: not that high

- search threads must posted by me
- search user
- push posts onto google sheet
- when post created, add new queue job to fetch post's comments
- daily scrape
