# ToDos:

## Pending
- Check that saves an already created source ok.
    - Saves when creator relevance changed
    - Saves when creator removed
    - Saves when type changed

- Creators Input
    - Throttle the 'creatorInput' call in logos editor

### Bugs


## Ongoin
- Check that saves an already created source ok.


## Done
- Check that saves an already created source ok.
    - Saves when attributes changed
    - Saves when creator attributes changed
    - Saves when creator Role changed

- Check that saves a new source OK
    - Saves attributes
    - Saves participations
        - Saves new participations
        - Saves modified participations
        - Saves modified creator attributes participations

- FIXED: - [8/11/21] Participations don't change when changing source for editing.
    - WHEN: select a source for edit; go back to list; select another source for edit
    - RESULT: the first participations still the same
    - EXPECTED: the participations should match the new selected source
