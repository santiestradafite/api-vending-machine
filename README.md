## Setup docker
`$ make install`

## Init doctrine schema
`$ make init-db`

## Run unit tests
`$ make test-unit`

## Postman endpoints
Import the postman collection provided on the root of the project (Vending_machine.postman_collection.json)
### Setup vending machine
    It creates a new vending machine with the items and coins configured on SetupVendingMachineCommandHandler, they can be modified there.
    In postman, enter a random uuid on the {{vendingMachineId}} collection var.

### Insert coin
    It adds a new coin with the value specified on the coin_value request param.
    Here's a useful query to get the current vending machine coins, but you can also use get vending machine endpoint:
```sql
select bin_to_uuid(id), value, is_inserted, is_returned
from vending_machine_coin
where vending_machine_id = uuid_to_bin('{{your_vending_machine_uuid}}');
```

### Return inserted coins
    It returns all inserted coins, but they are not returned in the response (see Get vending machine endpoint).

### Vend item
    It tries to vend the item with the uuid specified in the request param item_id.
    Possible errors:
        - The item is not found on the vending machine
        - The item has no stock
        - The inserted money is not enough
        - The vended item is still not collected (see Get vending machine endpoint)
    The vended item is not returned in the response (see Get vending machine endpoint).
    Here's a useful query to get the available item uuid's and copy the one you want to vend on the request param:
```sql
select bin_to_uuid(id), stock, name, price, is_vended
from vending_machine_item
where vending_machine_id = uuid_to_bin('{{your_vending_machine_uuid}}');
```
### Get vending machine
    Returns the vending machine current data:
        - Vending machine id
        - Vending machine name
        - Vended item:
            If there is any vended item it will be returned here, but on the next get vending machine call
            it won't be anymore, since the item is collected (cleared) on the first call.
        - Returned coins:
            If there are any coins returned when vending an item (change) or performing the return inserted coins operation.
            As well as the vended item, they are cleared, simulating the customer collects them.
        - Items:
            All items. If there's any vended item, it will also be here with the field is_vended = true,
            but I wanted to return it separately on vended_item as well to make it more visible.
        - Coins:
            All coins. If there's any returned coin, it will also be here with the field is_returned = truem
            but I also wanted to return them separately on returned_coins to make them more visible.
            Each con also has a field is_inserted to keep track of them before vending an item.